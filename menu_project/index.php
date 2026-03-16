<?php
$csvFile = __DIR__ . '/data.csv';
$rows = [];

if (file_exists($csvFile) && ($handle = fopen($csvFile, 'r')) !== false) {
    $headers = fgetcsv($handle);

    if ($headers !== false) {
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) === count($headers)) {
                $row = array_combine($headers, $data);

                $row['x'] = isset($row['x']) ? (float)$row['x'] : 0;
                $row['y'] = isset($row['y']) ? (float)$row['y'] : 0;
                $row['width'] = isset($row['width']) ? (float)$row['width'] : 240;
                $row['height'] = isset($row['height']) ? (float)$row['height'] : 120;
                $row['development_progress'] = isset($row['development_progress']) ? (int)$row['development_progress'] : 0;

                $rows[] = $row;
            }
        }
    }

    fclose($handle);
}

function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Chart from CSV</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrap">
    <div class="topbar">
        <div>
            <h1>Interactive Super FRS Project Chart</h1>
            <div class="subtitle">PHP + CSV + SVG + JavaScript</div>
        </div>

        <div class="controls">
            <select id="statusFilter">
                <option value="all">Show all</option>
                <option value="Development">Development</option>
                <option value="Test">Test</option>
                <option value="Production">Production</option>
                <option value="Blocked">Blocked</option>
            </select>

            <button id="downloadSvgBtn" type="button">Download SVG</button>
            <button id="downloadPdfBtn" type="button">Download PDF</button>
        </div>
    </div>

    <div class="layout">
        <div class="panel chart-panel">
            <div class="svg-wrap">
                <svg id="projectSvg" viewBox="0 0 1400 900" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="chartTitle">
                    <title id="chartTitle">Project chart loaded from CSV</title>

                    <defs>
                        <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#60a5fa"></stop>
                            <stop offset="100%" stop-color="#2563eb"></stop>
                        </linearGradient>
                    </defs>

                    <?php foreach ($rows as $row): ?>
                        <?php
                            $id = $row['id'] ?? '';
                            $title = $row['title'] ?? '';
                            $owner = $row['owner'] ?? '';
                            $status = $row['status'] ?? '';
                            $phase = $row['phase'] ?? '';
                            $progressDevelopment = max(0, min(100, (int)$row['development_progress']));
                            $description = $row['description'] ?? '';
                            $x = $row['x'];
                            $y = $row['y'];
                            $width = $row['width'];
                            $height = $row['height'];

                            $boxClass = 'neutral-box';
                            if ($status === 'Production') {
                                $boxClass = 'done-box';
                            } elseif ($status === 'Blocked') {
                                $boxClass = 'risk-box';
                            } elseif ($status === 'Test') {
                                $boxClass = 'phase-box';
                            }

                            $progressDevelopmentBarWidth = ($width - 40) * ($progressDevelopment / 100.0);
                        ?>
                        <g class="node"
                           data-id="<?= h($id) ?>"
                           data-title="<?= h($title) ?>"
                           data-owner="<?= h($owner) ?>"
                           data-status="<?= h($status) ?>"
                           data-phase="<?= h($phase) ?>"
                           data-progress="<?= h($progressDevelopment) ?>"
                           data-progress="<?= h($progressDevelopment) ?>"
                           data-description="<?= h($description) ?>"
                           transform="translate(<?= h($x) ?>, <?= h($y) ?>)">
                            
                            <rect class="<?= h($boxClass) ?>" width="<?= h($width) ?>" height="<?= h($height) ?>" rx="18" ry="18"></rect>

                            <text x="20" y="32" class="node-title"><?= h($title) ?></text>
                            <text x="20" y="56" class="node-small">Owner: <?= h($owner) ?></text>
                            <text x="20" y="78" class="node-small">Status: <?= h($status) ?></text>
                            <text x="20" y="100" class="node-small">Phase: <?= h($phase) ?></text>

                            <rect x="20" y="<?= h($height - 24) ?>" width="<?= h($width - 40) ?>" height="10" rx="5" ry="5" class="progress-bg"></rect>
                            <rect x="20" y="<?= h($height - 24) ?>" width="<?= h($progressDevelopmentBarWidth) ?>" height="10" rx="5" ry="5" fill="url(#progressGradient)"></rect>
                            <rect x="20" y="<?= h($height - 24) ?>" width="<?= h($progressDevelopmentBarWidth) ?>" height="10" rx="5" ry="15" fill="url(#progressGradient)"></rect>
                            <text x="<?= h($width - 48) ?>" y="<?= h($height - 32) ?>" class="progress-label"><?= h($progressDevelopment) ?>%</text>
                        </g>
                    <?php endforeach; ?>
                </svg>
            </div>
            <div class="footer-note">
                Click a box to show details. The data comes from <code>data.csv</code>.
            </div>
        </div>

        <div class="panel side-panel">
            <h2 id="detailsTitle">Details</h2>
            <p id="detailsDescription" class="hint">Select a project item.</p>

            <div class="meta">
                <div class="meta-card">
                    <div class="meta-label">Owner</div>
                    <div class="meta-value" id="metaOwner">—</div>
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
                    <div class="meta-label">Progress</div>
                    <div class="meta-value" id="metaProgress">—</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>