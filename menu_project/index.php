<?php
$csvFile = __DIR__ . '/data.csv';
$rows = [];

function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

if (file_exists($csvFile) && ($handle = fopen($csvFile, 'r')) !== false) {
    $headers = fgetcsv($handle);

    if ($headers !== false) {
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) === count($headers)) {
                $row = array_combine($headers, $data);

                $row['connect'] = trim($row['connect'] ?? '');
                $row['x'] = isset($row['x']) ? (float)$row['x'] : 0;
                $row['y'] = isset($row['y']) ? (float)$row['y'] : 0;
                $row['width'] = isset($row['width']) ? (float)$row['width'] : 260;
                $row['height'] = isset($row['height']) ? (float)$row['height'] : 140;

                $row['development_progress'] = max(0, min(100, (int)($row['development_progress'] ?? 0)));
                $row['test_progress'] = max(0, min(100, (int)($row['test_progress'] ?? 0)));
                $row['production_progress'] = max(0, min(100, (int)($row['production_progress'] ?? 0)));

                $rows[] = $row;
            }
        }
    }

    fclose($handle);

    $nodesById = [];

    foreach ($rows as $row) {
        $nodeId = $row['id'] ?? '';
        if ($nodeId !== '') {
            $nodesById[$nodeId] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Overview</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrap">
    <div class="topbar">
        <div>
            <h1>Super FRS interactive project chart</h1>
            <div class="subtitle">project description</div>
        </div>

        <div class="controls">

            <select id="statusFilter">
                <option value="all">All status</option>
                <option value="Development">Development</option>
                <option value="Tests">Tests</option>
                <option value="Production">Production</option>
                <option value="Completed">Completed</option>
            </select>

            <button id="downloadSvgBtn" type="button">Download SVG</button>
            <button id="downloadPdfBtn" type="button">Download PDF</button>

        </div>
    </div>

    <div class="layout">
        <div class="panel chart-panel">
            <div class="svg-wrap">
                <svg id="projectSvg" viewBox="0 0 1200 900" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="chartTitle">
                    <title id="chartTitle">Project cards with stacked progress bars</title>
<?php
$nodesById = [];
foreach ($rows as $r) {
    $nodeId = $r['id'] ?? '';
    if ($nodeId !== '') {
        $nodesById[$nodeId] = $r;
    }
}
?>

<?php foreach ($rows as $source): ?>
    <?php
    $sourceId = $source['id'] ?? '';
    $connectRaw = trim($source['connect'] ?? '');

    if ($sourceId === '' || $connectRaw === '') {
        continue;
    }

    $targets = array_filter(array_map('trim', explode(',', $connectRaw)));
    ?>

    <?php foreach ($targets as $targetId): ?>
        <?php
        if (!isset($nodesById[$targetId])) {
            continue;
        }

        $target = $nodesById[$targetId];

        $startX = $source['x'] + $source['width'];
        $startY = $source['y'] + $source['height'] / 2;

        $endX = $target['x'];
        $endY = $target['y'] + $target['height'] / 2;

        $dx = max(60, abs($endX - $startX) * 0.35);

        $pathD = sprintf(
            'M %.1f %.1f C %.1f %.1f, %.1f %.1f, %.1f %.1f',
            $startX, $startY,
            $startX + $dx, $startY,
            $endX - $dx, $endY,
            $endX, $endY
        );
        ?>
        <path class="connector" d="<?= h($pathD) ?>" marker-end="url(#arrowhead)"></path>
    <?php endforeach; ?>
<?php endforeach; ?>
                    <defs>
                        <linearGradient id="gradDev" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#93c5fd"></stop>
                            <stop offset="100%" stop-color="#2563eb"></stop>
                        </linearGradient>

                        <linearGradient id="gradTest" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#c4b5fd"></stop>
                            <stop offset="100%" stop-color="#7c3aed"></stop>
                        </linearGradient>

                        <linearGradient id="gradProd" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#86efac"></stop>
                            <stop offset="100%" stop-color="#059669"></stop>
                        </linearGradient>
                    </defs>

                    <?php foreach ($rows as $row): ?>
                        <?php
                            $id = $row['id'] ?? '';
                            $title = $row['title'] ?? '';
                            $subwpl = $row['subwpl'] ?? '';
                            $status = $row['status'] ?? '';
                            $phase = $row['phase'] ?? '';
                            $description = $row['description'] ?? '';

                            $x = $row['x'];
                            $y = $row['y'];
                            $width = $row['width'];
                            $height = $row['height'];

                            $dev = $row['development_progress'];
                            $test = $row['test_progress'];
                            $prod = $row['production_progress'];

                            $barX = 20;
                            $barW = $width - 70;
                            $barH = 10;

                            $devY = 86;
                            $testY = 104;
                            $prodY = 122;
                        ?>
                        <g class="node"
                           data-id="<?= h($id) ?>"
                           data-title="<?= h($title) ?>"
                           data-subwpl="<?= h($subwpl) ?>"
                           data-status="<?= h($status) ?>"
                           data-phase="<?= h($phase) ?>"
                           data-description="<?= h($description) ?>"
                           data-development="<?= h($dev) ?>"
                           data-tests="<?= h($test) ?>"
                           data-production="<?= h($prod) ?>"
                           transform="translate(<?= h($x) ?>, <?= h($y) ?>)">

                            <rect class="card-box" width="<?= h($width) ?>" height="<?= h($height) ?>" rx="18" ry="18"></rect>

                            <text x="16" y="24" class="node-title"><?= h($title) ?></text>
                            <text x="16" y="46" class="node-small">Sub WPL: <?= h($subwpl) ?></text>
                            <text x="16" y="66" class="node-small">Status: <?= h($status) ?></text>

                            <text x="<?= h($width - 16) ?>" y="<?= h($devY + $barH / 2 + 4) ?>" text-anchor="end" class="percent-top"><?= h($dev) ?>%</text>
                            <rect x="<?= h($barX) ?>" y="<?= h($devY) ?>" width="<?= h($barW) ?>" height="<?= h($barH) ?>" rx="5" ry="5" class="progress-bg"></rect>
                            <rect x="<?= h($barX) ?>" y="<?= h($devY) ?>" width="<?= h($barW * $dev / 100) ?>" height="<?= h($barH) ?>" rx="5" ry="5" fill="url(#gradDev)"></rect>

                            <text x="<?= h($width - 16) ?>" y="<?= h($testY + $barH / 2 + 4) ?>" text-anchor="end" class="percent-top"><?= h($test) ?>%</text>
                            <rect x="<?= h($barX) ?>" y="<?= h($testY) ?>" width="<?= h($barW) ?>" height="<?= h($barH) ?>" rx="5" ry="5" class="progress-bg"></rect>
                            <rect x="<?= h($barX) ?>" y="<?= h($testY) ?>" width="<?= h($barW * $test / 100) ?>" height="<?= h($barH) ?>" rx="5" ry="5" fill="url(#gradTest)"></rect>

                            <text x="<?= h($width - 16) ?>" y="<?= h($prodY + $barH / 2 + 4) ?>" text-anchor="end" class="percent-top"><?= h($prod) ?>%</text>
                            <rect x="<?= h($barX) ?>" y="<?= h($prodY) ?>" width="<?= h($barW) ?>" height="<?= h($barH) ?>" rx="5" ry="5" class="progress-bg"></rect>
                            <rect x="<?= h($barX) ?>" y="<?= h($prodY) ?>" width="<?= h($barW * $prod / 100) ?>" height="<?= h($barH) ?>" rx="5" ry="5" fill="url(#gradProd)"></rect>
                        </g>
                    <?php endforeach; ?>
                </svg>
            </div>

            <div class="footer-note">
                <div class="copyright">
                    <p>&copy; 2026 Irakli Keshelashvili. All rights reserved.</p>
                </div>
            </div>
        </div>

        <div class="panel side-panel">
            <h2 id="detailsTitle">Details</h2>
            <p id="detailsDescription" class="hint">Click a project card.</p>

            <div class="meta">
                <div class="meta-card">
                    <div class="meta-label">Sub WPL</div>
                    <div class="meta-value" id="metaSubWPL">—</div>
                </div>

                <div class="meta-card">
                    <div class="meta-label">Status</div>
                    <div class="meta-value" id="metaStatus">—</div>
                </div>

                <div class="meta-card">
                    <div class="meta-label">Phase</div>
                    <div class="meta-value" id="metaPhase">—</div>
                </div>

                <div class="meta-card">
                    <div class="meta-label">Development</div>
                    <div class="meta-value" id="metaDevelopment">—</div>
                </div>

                <div class="meta-card">
                    <div class="meta-label">Tests</div>
                    <div class="meta-value" id="metaTests">—</div>
                </div>

                <div class="meta-card">
                    <div class="meta-label">Production</div>
                    <div class="meta-value" id="metaProduction">—</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>