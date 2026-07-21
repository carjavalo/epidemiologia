$ErrorActionPreference = 'Stop'
$root = Get-Location
$outputDir = Join-Path $root 'docu\documentacion_entrega'
$assetsDir = Join-Path $outputDir '_assets'
$membrete  = Join-Path $assetsDir 'membrete.png'
$jsonPath  = Join-Path $assetsDir 'contenido.json'

if (-not (Test-Path $membrete)) { throw "Falta el membrete: $membrete" }
if (-not (Test-Path $jsonPath))  { throw "Falta el contenido JSON: $jsonPath" }

# Read JSON as UTF-8 to preserve accents
$docs = Get-Content -Raw -Encoding UTF8 -LiteralPath $jsonPath | ConvertFrom-Json

# Palette
$NAVY  = '1F3864'
$BLUE  = '2E74B5'
$LIGHT = 'D9E2F3'
$GRAY  = '595959'

function XmlEscape($s) { [System.Security.SecurityElement]::Escape([string]$s) }

function WriteUtf8($path, $content) {
    $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($path, $content, $utf8NoBom)
}

function Heading($texto) {
    $t = XmlEscape $texto
    return '<w:p><w:pPr><w:pStyle w:val="Heading1"/><w:pBdr><w:left w:val="single" w:sz="36" w:space="6" w:color="' + $BLUE + '"/></w:pBdr><w:shd w:val="clear" w:color="auto" w:fill="' + $LIGHT + '"/><w:spacing w:before="360" w:after="180"/><w:ind w:left="120"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:b/><w:color w:val="' + $NAVY + '"/><w:sz w:val="28"/></w:rPr><w:t xml:space="preserve">' + $t + '</w:t></w:r></w:p>'
}

function Parrafo($texto) {
    $t = XmlEscape $texto
    return '<w:p><w:pPr><w:jc w:val="both"/><w:spacing w:after="160" w:line="312" w:lineRule="auto"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="22"/><w:color w:val="1F2433"/></w:rPr><w:t xml:space="preserve">' + $t + '</w:t></w:r></w:p>'
}

function CajaResumen($texto) {
    $t = XmlEscape $texto
    $etiqueta = XmlEscape 'Resumen:  '
    return '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="' + $BLUE + '"/><w:left w:val="single" w:sz="4" w:space="0" w:color="' + $BLUE + '"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="' + $BLUE + '"/><w:right w:val="single" w:sz="4" w:space="0" w:color="' + $BLUE + '"/></w:tblBorders><w:tblCellMar><w:top w:w="160" w:type="dxa"/><w:left w:w="200" w:type="dxa"/><w:bottom w:w="160" w:type="dxa"/><w:right w:w="200" w:type="dxa"/></w:tblCellMar></w:tblPr><w:tblGrid><w:gridCol w:w="9000"/></w:tblGrid><w:tr><w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:shd w:val="clear" w:color="auto" w:fill="' + $LIGHT + '"/></w:tcPr><w:p><w:pPr><w:jc w:val="both"/><w:spacing w:after="0"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:b/><w:color w:val="' + $NAVY + '"/><w:sz w:val="22"/></w:rPr><w:t xml:space="preserve">' + $etiqueta + '</w:t></w:r><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:color w:val="' + $GRAY + '"/><w:sz w:val="22"/></w:rPr><w:t xml:space="preserve">' + $t + '</w:t></w:r></w:p></w:tc></w:tr></w:tbl><w:p/>'
}

function Portada($titulo, $subtitulo, $resumen) {
    $t = XmlEscape $titulo
    $s = XmlEscape $subtitulo
    $hoy = (Get-Date).ToString('dd \d\e MMMM \d\e yyyy', [System.Globalization.CultureInfo]::GetCultureInfo('es-ES'))
    $oacute = [char]0x00F3
    $fecha = XmlEscape ('Fecha de elaboraci' + $oacute + 'n: ' + $hoy)

    $xml  = ''
    # Espacio superior para que la portada quede debajo del membrete
    $xml += '<w:p><w:pPr><w:spacing w:before="2400" w:after="0"/></w:pPr></w:p>'
    # Bloque azul oscuro con titulo y subtitulo
    $xml += '<w:tbl><w:tblPr><w:tblW w:w="5000" w:type="pct"/><w:tblBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="' + $NAVY + '"/><w:left w:val="single" w:sz="4" w:space="0" w:color="' + $NAVY + '"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="' + $NAVY + '"/><w:right w:val="single" w:sz="4" w:space="0" w:color="' + $NAVY + '"/></w:tblBorders><w:tblCellMar><w:top w:w="500" w:type="dxa"/><w:left w:w="400" w:type="dxa"/><w:bottom w:w="500" w:type="dxa"/><w:right w:w="400" w:type="dxa"/></w:tblCellMar></w:tblPr><w:tblGrid><w:gridCol w:w="9000"/></w:tblGrid><w:tr><w:tc><w:tcPr><w:tcW w:w="5000" w:type="pct"/><w:shd w:val="clear" w:color="auto" w:fill="' + $NAVY + '"/></w:tcPr>'
    $xml += '<w:p><w:pPr><w:jc w:val="center"/><w:spacing w:after="120"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:b/><w:color w:val="FFFFFF"/><w:sz w:val="48"/></w:rPr><w:t xml:space="preserve">' + $t + '</w:t></w:r></w:p>'
    $xml += '<w:p><w:pPr><w:jc w:val="center"/><w:spacing w:after="0"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:color w:val="DCE6F1"/><w:sz w:val="28"/></w:rPr><w:t xml:space="preserve">' + $s + '</w:t></w:r></w:p>'
    $xml += '</w:tc></w:tr></w:tbl>'
    $xml += '<w:p><w:pPr><w:jc w:val="center"/><w:spacing w:before="240" w:after="0"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:i/><w:color w:val="' + $GRAY + '"/><w:sz w:val="22"/></w:rPr><w:t xml:space="preserve">' + $fecha + '</w:t></w:r></w:p>'
    $xml += '<w:p><w:pPr><w:spacing w:before="480" w:after="0"/></w:pPr></w:p>'
    $xml += (CajaResumen $resumen)
    # Salto de pagina
    $xml += '<w:p><w:r><w:br w:type="page"/></w:r></w:p>'
    return $xml
}

# Header XML with full-page background image (membrete)
$headerXml = @'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:hdr xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:p><w:pPr><w:spacing w:after="0" w:before="0"/></w:pPr><w:r><w:drawing><wp:anchor distT="0" distB="0" distL="0" distR="0" simplePos="0" allowOverlap="1" behindDoc="1" locked="0" layoutInCell="1" relativeHeight="1"><wp:simplePos x="0" y="0"/><wp:positionH relativeFrom="page"><wp:posOffset>0</wp:posOffset></wp:positionH><wp:positionV relativeFrom="page"><wp:posOffset>0</wp:posOffset></wp:positionV><wp:extent cx="7772400" cy="10058400"/><wp:effectExtent t="0" r="0" b="0" l="0"/><wp:wrapNone/><wp:docPr id="1" name="Membrete"/><wp:cNvGraphicFramePr><a:graphicFrameLocks noChangeAspect="1"/></wp:cNvGraphicFramePr><a:graphic><a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture"><pic:pic><pic:nvPicPr><pic:cNvPr id="0" name="Membrete"/><pic:cNvPicPr><a:picLocks noChangeAspect="1" noChangeArrowheads="1"/></pic:cNvPicPr></pic:nvPicPr><pic:blipFill><a:blip r:embed="rIdMembrete"/><a:srcRect/><a:stretch><a:fillRect/></a:stretch></pic:blipFill><pic:spPr bwMode="auto"><a:xfrm><a:off x="0" y="0"/><a:ext cx="7772400" cy="10058400"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr></pic:pic></a:graphicData></a:graphic></wp:anchor></w:drawing></w:r></w:p></w:hdr>
'@

$headerRels = @'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rIdMembrete" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/membrete.png"/></Relationships>
'@

$footerXml = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:ftr xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:p><w:pPr><w:jc w:val="center"/><w:spacing w:before="0" w:after="0"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:color w:val="$GRAY"/><w:sz w:val="18"/></w:rPr><w:t xml:space="preserve">P&#225;gina </w:t></w:r><w:r><w:fldChar w:fldCharType="begin"/></w:r><w:r><w:instrText xml:space="preserve">PAGE</w:instrText></w:r><w:r><w:fldChar w:fldCharType="end"/></w:r></w:p></w:ftr>
"@

$stylesXml = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:docDefaults><w:rPrDefault><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial" w:eastAsia="Arial"/><w:color w:val="1F2433"/><w:sz w:val="22"/><w:szCs w:val="22"/><w:lang w:val="es-CO"/></w:rPr></w:rPrDefault><w:pPrDefault><w:pPr><w:spacing w:after="160" w:line="276" w:lineRule="auto"/></w:pPr></w:pPrDefault></w:docDefaults><w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/><w:qFormat/></w:style><w:style w:type="paragraph" w:styleId="Heading1"><w:name w:val="heading 1"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:qFormat/><w:pPr><w:keepNext/><w:spacing w:before="360" w:after="120"/><w:outlineLvl w:val="0"/></w:pPr><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:b/><w:color w:val="$NAVY"/><w:sz w:val="28"/></w:rPr></w:style></w:styles>
"@

$contentTypes = @'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Default Extension="png" ContentType="image/png"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/><Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/><Override PartName="/word/header1.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.header+xml"/><Override PartName="/word/footer1.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml"/></Types>
'@

$rootRels = @'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>
'@

$docRels = @'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/><Relationship Id="rId10" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/header" Target="header1.xml"/><Relationship Id="rId11" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/footer" Target="footer1.xml"/></Relationships>
'@

function New-Docx($path, $doc) {
    if (Test-Path $path) {
        try { Remove-Item $path -Force -ErrorAction Stop }
        catch { Write-Warning "Archivo bloqueado, omitiendo: $path"; return }
    }
    $temp = Join-Path $env:TEMP ([guid]::NewGuid().ToString())
    New-Item -ItemType Directory -Path $temp | Out-Null
    New-Item -ItemType Directory -Path (Join-Path $temp '_rels') | Out-Null
    New-Item -ItemType Directory -Path (Join-Path $temp 'word\_rels') -Force | Out-Null
    New-Item -ItemType Directory -Path (Join-Path $temp 'word\media') -Force | Out-Null

    # Body
    $body = ''
    $body += Portada $doc.titulo $doc.subtitulo $doc.resumen
    foreach ($seccion in $doc.secciones) {
        $body += Heading $seccion.titulo
        foreach ($p in $seccion.parrafos) {
            $body += Parrafo $p
        }
    }

    # Section properties: A4 with header/footer references and large top margin
    # to leave room for institutional letterhead at top of every page.
    $sectPr = '<w:sectPr><w:headerReference w:type="default" r:id="rId10"/><w:footerReference w:type="default" r:id="rId11"/><w:pgSz w:w="12240" w:h="15840"/><w:pgMar w:top="2200" w:right="1440" w:bottom="1440" w:left="1440" w:header="0" w:footer="720" w:gutter="0"/><w:cols w:space="708"/><w:docGrid w:linePitch="360"/></w:sectPr>'

    $document = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><w:body>' + $body + $sectPr + '</w:body></w:document>'

    WriteUtf8 (Join-Path $temp '[Content_Types].xml')          $contentTypes
    WriteUtf8 (Join-Path $temp '_rels\.rels')                  $rootRels
    WriteUtf8 (Join-Path $temp 'word\_rels\document.xml.rels') $docRels
    WriteUtf8 (Join-Path $temp 'word\_rels\header1.xml.rels') $headerRels
    WriteUtf8 (Join-Path $temp 'word\document.xml')            $document
    WriteUtf8 (Join-Path $temp 'word\styles.xml')              $stylesXml
    WriteUtf8 (Join-Path $temp 'word\header1.xml')             $headerXml
    WriteUtf8 (Join-Path $temp 'word\footer1.xml')             $footerXml
    Copy-Item -LiteralPath $membrete -Destination (Join-Path $temp 'word\media\membrete.png') -Force

    Add-Type -AssemblyName System.IO.Compression.FileSystem
    [System.IO.Compression.ZipFile]::CreateFromDirectory($temp, $path)
    Remove-Item $temp -Recurse -Force
}

foreach ($prop in $docs.PSObject.Properties) {
    $name = $prop.Name
    $doc  = $prop.Value
    $out  = Join-Path $outputDir ($name + '.docx')
    New-Docx $out $doc
    Write-Output ('Creado: ' + $name + '.docx')
}
