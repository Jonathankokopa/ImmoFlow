<?php
$zip = new ZipArchive();
if ($zip->open(__DIR__ . '/docs/Real_Estate_Management_System_User_Manual.docx') !== true) {
    fwrite(STDERR, "Unable to open DOCX\n"); exit(1);
}
foreach (['word/document.xml', 'word/styles.xml', 'word/numbering.xml', 'word/settings.xml', 'word/footer1.xml', '[Content_Types].xml'] as $name) {
    $xml = $zip->getFromName($name);
    $dom = new DOMDocument();
    if ($xml === false || !$dom->loadXML($xml)) {
        fwrite(STDERR, "Invalid XML: $name\n"); exit(2);
    }
    echo "Validated $name\n";
}
echo "DOCX package OK\n";
