<?php if (!defined('ABSPATH')) exit; ?>
<?php
if ( isset($mw_security_data['response']['response']) && is_array($mw_security_data['response']['response'])): ?>
<div class="wrap">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<h2>Malware-Secured Report</h2>
<?php
$mw_security_data = $mw_security_data['response'];
$cleanedCount = 0;
$quarantinedCount = 0;
$compromised = 0;
$malicious = 0;
$malwaretypeuploader = 0;
$malwaretypewebshell = 0;
$malwaretypeadware = 0;
$malwaretypephishing = 0;
$otherMalware = 0;
$allCount = 0;
$uploader = isset($types['uploader']) ? $types['uploader'] : 0;
$webshell = isset($types['webshell']) ? $types['webshell'] : 0;
$adware = isset($types['adware']) ? $types['adware'] : 0;
$phishing = isset($types['phishing']) ? $types['phishing'] : 0;
$other = isset($types['other']) ? $types['other'] : 0;
$quarantined = isset($quarantined) ? $quarantined : 0;
$cleaned = isset($cleaned) ? $cleaned : 0;
foreach ($mw_security_data['response'] as $fileEvent) {
    // Normalize action and count
    $action = strtolower($fileEvent['event']['action'] ?? '');
    if ($action === 'cleaned') {
        $cleanedCount++;
    } elseif ($action === 'quarantined') {
        $quarantinedCount++;
    }

    // Classification counts
    $classification = $fileEvent['analysis']['cloud']['classification'] ?? '';
    if ($classification === 'compromised') {
        $compromised++;
    } elseif ($classification === 'malicious') {
        $malicious++;
    }

    // Tag-based malware type counts (search entire tags array)
    $tags = $fileEvent['analysis']['cloud']['tags'] ?? [];
    $knownTypes = ['uploader', 'webshell', 'adware', 'phishing'];
    $foundKnown = false;

    if (in_array('uploader', $tags, true)) {
        $malwaretypeuploader++;
        $foundKnown = true;
    }
    if (in_array('webshell', $tags, true)) {
        $malwaretypewebshell++;
        $foundKnown = true;
    }
    if (in_array('adware', $tags, true)) {
        $malwaretypeadware++;
        $foundKnown = true;
    }
    if (in_array('phishing', $tags, true)) {
        $malwaretypephishing++;
        $foundKnown = true;
    }

    $unknownTags = array_diff($tags, $knownTypes);
    if (!$foundKnown && !empty($unknownTags)) {
        $otherMalware++;
    }
    $allCount++;
}
?>
<?php $enabled = filter_var(get_option('mw_force_footer_enabled', true), FILTER_VALIDATE_BOOLEAN);
    if (!$enabled){?>
        <div class="mw-row mw-card mw-justify-content-space-between">
            <div class="sec-block sec-shield mw-row mw-col-12 mw-align-items-center">
                <div><img style="vertical-align: middle;padding-right: 8px;" src="<?php echo esc_url(MILESWEB_PLUGIN_ASSETS_URL) ?>images/warning.png" alt="Warning | MilesWeb"></div>
                <div class="pl-10">
                    <div class="sec-shield-title">Warning:</div>
                    <div class="sec-shield-p pt-10">Malware protection is turned off. Reactivate to secure your website. <a href="<?php echo esc_url(admin_url('admin.php?page=milesweb')); ?>">Click here</a> to enable.</div>
                </div>
            </div>
        </div>
    <?php }else { ;?>
<div class="mw-row mw-card mw-justify-content-space-between">
    <div class="mw-row mw-col-12 pt-10 mw-col-xxl-8">
        <div class="mw-row mw-col-12 pt-10">
            <div class="sec-block sec-shield mw-row mw-col-12 mw-align-items-center">
                <div><img style="vertical-align: middle;padding-right: 8px;" src="<?php echo esc_url(MILESWEB_PLUGIN_ASSETS_URL) ?>images/security-shield.png" alt="Antivirus | MilesWeb"></div>
                <div class="pl-10">
                    <div class="sec-shield-title">Your site is free of malware!</div>
                    <div class="sec-shield-p pt-10">MilesWeb is automatically protecting your site from attack</div>
                </div>
            </div>
        </div>

        <div class="mw-row mw-col-12 mw-col-xl-4 mw-col-sm-6 pt-22">
            <h4 class="sec-block-title pl-10"><img style="vertical-align: middle;padding-right: 8px;" src="<?php echo esc_url(MILESWEB_PLUGIN_ASSETS_URL) ?>images/antivirus.svg" alt="Antivirus | MilesWeb">Antivirus</h4>
            <div class="mw-row mw-justify-content-space-between mw-align-items-center sec-block sec-shield-box">
                <div class="mw-row mw-align-items-center mw-justify-content-space-between">
                    <h2>Threat Detection</h2>
                    <div class="sec-stat"><?php echo esc_html($malicious + $compromised);?></div>
                </div>
                <p>The threats identified in on-disk files by classification.</p>
                <div class="mw-row mw-justify-content-space-between mw-align-items-center">
                    <canvas id="detectionDonut" class="donut-chart"></canvas>
                    <div class="sec-terms-wrap">
                        <div data-filter="Malicious" data-column="4" class="mw-row mw-align-items-center mw-justify-content-space-between sec-terms-box">
                            <div>Malicious</div><span class="sec-danger"><?php echo esc_html( $malicious ); ?></span>
                        </div>
                        <div data-filter="Compromised" data-column="4" class="mw-row mw-align-items-center mw-justify-content-space-between sec-terms-box">
                            <div>Compromised</div><span class="sec-safe"><?php echo esc_html( $compromised );?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mw-row mw-col-12 mw-col-xl-4 mw-col-sm-6 pt-22">
            <h4 class="sec-block-title pl-10"><img style="vertical-align: middle;padding-right: 8px;" src="<?php echo esc_url(MILESWEB_PLUGIN_ASSETS_URL) ?>images/remediation.svg" alt="Remediation | MilesWeb">Remediation</h4>
            <div class="mw-row mw-justify-content-space-between mw-align-items-center sec-block sec-shield-box">
                <div class="mw-row mw-align-items-center mw-justify-content-space-between">
                    <h2>Threat Removal</h2>
                    <div class="sec-stat"><?php echo esc_html( $quarantinedCount + $cleanedCount);?></div>
                </div>
                <p>The breakdown of discovered on-disk files by classification.</p>
                <div class="mw-row mw-justify-content-space-between mw-align-items-center">
                    <canvas id="remediationDonut" class="donut-chart"></canvas>
                    <div class="sec-terms-wrap">
                        <div data-filter="Quarantined" data-column="4" class="mw-row mw-align-items-center mw-justify-content-space-between sec-terms-box">
                            <div>Quarantined</div><span class="sec-danger"><?php echo esc_html( $quarantinedCount ); ?></span>
                        </div>
                        <div data-filter="Cleaned" data-column="4" class="mw-row mw-align-items-center mw-justify-content-space-between sec-terms-box">
                            <div>Cleaned</div><span class="sec-safe"><?php echo esc_html( $cleanedCount );?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mw-row mw-col-12 mw-col-xl-4 mw-col-sm-6 pt-22">
            <h4 class="sec-block-title pl-10">Malware Types</h4>
            <div class="mw-row mw-justify-content-space-between mw-align-items-center sec-block sec-shield-box">
                <div class="mw-row mw-align-items-center mw-justify-content-space-between">
                <div class="sec-malware-wrap">
                    <div class="sec-malware-count uploader"><span>uploader</span> <span><?php echo esc_html( $malwaretypeuploader ); ?></span></div>
                    <div class="sec-malware-count webshell"><span>webshell</span> <span><?php echo esc_html( $malwaretypewebshell ); ?></span></div>
                    <div class="sec-malware-count adware"><span>adware</span> <span><?php echo esc_html( $malwaretypeadware ); ?></span></div>
                    <div class="sec-malware-count phishing"><span>phishing</span> <span><?php echo esc_html( $malwaretypephishing ); ?></span></div>
                    <div class="sec-malware-count other"><span>other</span> <span><?php echo esc_html( $otherMalware ); ?></span></div>
                </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mw-row mw-col-12 pt-10 mw-col-xxl-4">
        <!-- Malware Types -->
        <div class="mw-row mw-col-12 pt-22">
            <div class="sec-block mw-col-12 pt-22 mw-mt-0">
                <h4 class="sec-block-title">Protection Enabled</h4>
                <p class="sec-safe-info">On-disk files are automatically scanned for malware and remediated</p>
                <p class="sec-note">Think your website might be hacked or still infected with malware? Here's where to start:</p>
                <ul class="sec-checklist">
                    <li>Clean all database injections</li>
                    <li>Clear website cache</li>
                    <li>Remove malicious artifacts</li>
                    <li>Restore core application files</li>
                    <li>Purge malicious database users</li>
                </ul>
            </div>
        </div>
    </div>
</div>


<script>
// ChartJS
milesweb_get_storage_info();
const classificationChart = new Chart(document.getElementById('classificationChart'), {
    type: 'doughnut',
    data: {
        labels: ['Malicious', 'Quarantined', 'Cleaned'],
        datasets: [{
            data: [<?php echo esc_js( $malicious ); ?>, <?php echo esc_js( $quarantined ); ?>, <?php echo esc_js( $cleaned ); ?>],
            backgroundColor: ['#ef4444', '#f59e0b', '#10b981']
        }]
    }
});

const malwareChart = new Chart(document.getElementById('malwareTypeChart'), {
    type: 'doughnut',
    data: {
        labels: ['Uploader', 'Webshell', 'Adware', 'Phishing', 'Other'],
        datasets: [{
            data: [<?php echo esc_js($uploader); ?>,<?php echo esc_js($webshell); ?>,<?php echo esc_js($adware); ?>,<?php echo esc_js($phishing); ?>,<?php echo esc_js($other); ?>],
            backgroundColor: ['#3b82f6', '#9333ea', '#eab308', '#14b8a6', '#6b7280']
        }]
    }
});
</script>


<script>
    const opts = {
        type: 'doughnut',
        options: {
            cutout: '75%', responsive: false,
            plugins: { legend: { display: false } }
        }
    };

const compromised = <?php echo esc_js( $compromised ); ?>;
const malicious = <?php echo esc_js( $malicious ); ?>;
let data, backgroundColors, labels;
if (compromised === 0 && malicious === 0) {
    data = [1]; backgroundColors = ['#cccccc'];labels = ['No Data'];
} else {
  data = [compromised, malicious];backgroundColors = ['#2a9d8f', '#e63946'];labels = ['Compromised', 'Malicious'];
}
new Chart(document.getElementById('detectionDonut'), {
  ...opts,
  data: {
    labels: labels,
    datasets: [{
      data: data,
      backgroundColor: backgroundColors
    }]
  }
});

const cleanedCount = <?php echo esc_js( $cleanedCount ); ?>;
const quarantinedCount = <?php echo esc_js( $quarantinedCount ); ?>;
let remediationData, remediationBackgroundColors, remediationLabels;
if (cleanedCount === 0 && quarantinedCount === 0) {
  remediationData = [1];remediationBackgroundColors = ['#cccccc'];remediationLabels = ['No Data'];
} else {
  remediationData = [cleanedCount, quarantinedCount];remediationBackgroundColors = ['#2a9d8f', '#e63946'];remediationLabels = ['Cleaned', 'Quarantined'];
}
new Chart(document.getElementById('remediationDonut'), {
    ...opts,
    data: {
        labels: remediationLabels,
        datasets: [{
            data: remediationData,
            backgroundColor: remediationBackgroundColors
        }]
    }
});

</script>
 <?php };?>

<?php else: ?>
        <div class="mw-row mw-card mw-justify-content-space-between">
            <div class="sec-block sec-shield mw-row mw-col-12 mw-align-items-center">
                <div><img style="vertical-align: middle;padding-right: 8px;" src="<?php echo esc_url(MILESWEB_PLUGIN_ASSETS_URL) ?>images/warning.png" alt="Warning | MilesWeb"></div>
                <div class="pl-10">
                    <div class="sec-shield-title">Warning:</div>
                    <div class="sec-shield-p pt-10">Malware protection is turned off. Reactivate to secure your website. No Security events found.</div>
                </div>
            </div>
        </div>
<?php endif; ?>

