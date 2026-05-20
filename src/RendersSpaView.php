<?php

namespace Sakib\LaravelSpa;

trait RendersSpaView
{
    protected function renderSpa(string $view, array $data = [])
    {
        $viewResponse = view($view, $data);

        if (request()->header('X-Frontend-SPA') === 'true') {
            $sections = $viewResponse->renderSections();

            if (empty(trim($sections['content'] ?? ''))) {
                $fullHtml = $viewResponse->render();

                // safe content extraction without nested main issue
                $content = '';
                $start = strpos($fullHtml, 'data-spa-content');
                if ($start !== false) {
                    $tagStart = strrpos(substr($fullHtml, 0, $start), '<');
                    $innerStart = strpos($fullHtml, '>', $start) + 1;
                    $innerEnd = strrpos($fullHtml, '</main>');
                    if ($innerStart && $innerEnd) {
                        $content = trim(substr($fullHtml, $innerStart, $innerEnd - $innerStart));
                    }
                }

                preg_match_all('/<style(?![^>]*data-spa-layout-style)[^>]*>.*?<\/style>/si', $fullHtml, $styleMatches);
                $style = implode("\n", $styleMatches[0] ?? []);

                preg_match_all('/<script(?![^>]*data-spa-layout-script)(?![^>]*\bsrc=)[^>]*>.*?<\/script>/si', $fullHtml, $scriptMatches);
                $script = implode("\n", $scriptMatches[0] ?? []);

                preg_match('/<title>(.*?)<\/title>/si', $fullHtml, $titleMatch);
                $rawTitle = strip_tags($titleMatch[1] ?? config('app.name'));
                $title = trim(explode(' - ', $rawTitle)[0]);

                return response()->json([
                    'title'   => $title,
                    'style'   => $style,
                    'content' => $content,
                    'script'  => $script,
                ]);
            }

            $style  = preg_replace('/<style(?![^>]*data-spa-style)/', '<style data-spa-style', $sections['style'] ?? '');
            $script = preg_replace('/<script(?![^>]*data-spa-page-script)/', '<script data-spa-page-script', $sections['script'] ?? '');

            $rawTitle = strip_tags($sections['title'] ?? config('app.name'));
            $title    = trim(explode(' - ', $rawTitle)[0]);

            return response()->json([
                'title'   => $title,
                'style'   => $style,
                'content' => $sections['content'] ?? '',
                'script'  => $script,
            ]);
        }

        return $viewResponse;
    }
}