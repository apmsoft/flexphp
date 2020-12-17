<?php
function headerHTML ($title) {
    $outHeaderHtml = "
    <!DOCTYPE html>
    <html>
    <head>

    <meta charset=\"utf-8\" name=\"viewport\" content=\"width=device-width, initial-scale=1.0, user-scalable=no\">
    <title>{$title}</title>

    </head>
    <body>
    ";

    return $outHeaderHtml;
}
?>
