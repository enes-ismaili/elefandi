@php
//    $templateContent = Str::replace('{{$content}}', $content, $template);
//    $templateContent = Str::replace('{{$content}}', $content, $template);
//    preg_replace($pattern, $replacement, $string);
    global $gContent;
    $gContent = $content;
    function getVariable($matches) {
        global $gContent;
        if(isset($gContent[$matches[1]])){
            return $gContent[$matches[1]];
        } else {
            return "";
        }
    }
   $templateContent = preg_replace_callback('/\{\$(.*?)\}/m', 'getVariable', $ptemplate);
   $templateFullContent = preg_replace_callback('/\{\$(.*?)\}/m', 'getVariable', $templateContent);
@endphp
{!! $templateFullContent !!}
