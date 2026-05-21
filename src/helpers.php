<?php

if (!function_exists('spa')) {
    function spa(string $view, array $data = [])
    {
        $viewResponse = view($view, $data);

        if (request()->header('X-Frontend-SPA') === 'true') {
            $sections = $viewResponse->renderSections();

            if (empty(trim($sections['content'] ?? ''))) {
                $fullHtml = $viewResponse->render();

                $content = '';
                $start = strpos($fullHtml, 'data-spa-content');
                if ($start !== false) {
                    $openTag = strrpos(substr($fullHtml, 0, $start), '<');
                    $tagNameMatch = [];
                    preg_match('/<(\w+)\s[^>]*data-spa-content/', substr($fullHtml, $openTag), $tagNameMatch);
                    $tagName = $tagNameMatch[1] ?? 'div';
                    $innerStart = strpos($fullHtml, '>', $start) + 1;
                    $innerEnd = strrpos($fullHtml, '</' . $tagName . '>');
                    if ($innerStart && $innerEnd) {
                        $content = trim(substr($fullHtml, $innerStart, $innerEnd - $innerStart));
                    }
                }

                preg_match_all('/<style(?![^>]*data-spa-layout-style)[^>]*>.*?<\/style>/si', $fullHtml, $styleMatches);
                $style = implode("\n", $styleMatches[0] ?? []);

                preg_match_all('/<script(?![^>]*data-spa-layout-script)(?![^>]*\bsrc=)[^>]*>.*?<\/script>/si', $fullHtml, $scriptMatches);
                $script = implode("\n", $scriptMatches[0] ?? []);

                preg_match('/<title>(.*?)<\/title>/si', $fullHtml, $titleMatch);
                $title = strip_tags($titleMatch[1] ?? config('app.name'));

                return response()->json([
                    'title'   => $title,
                    'style'   => $style,
                    'content' => $content,
                    'script'  => $script,
                ]);
            }

            $style  = preg_replace('/<style(?![^>]*data-spa-style)/', '<style data-spa-style', $sections['style'] ?? '');
            $script = preg_replace('/<script(?![^>]*data-spa-page-script)/', '<script data-spa-page-script', $sections['script'] ?? '');
            $title  = strip_tags($sections['title'] ?? config('app.name'));

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