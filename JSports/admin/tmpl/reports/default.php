<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.keepalive');

$app   = Factory::getApplication();
$token = $app->getFormToken();

// Example program list – normally passed from View or Service
$programs2 = [
    37 => '2026 Spring Baseball',
    35 => '2025 Spring   Baseball',
];
?>

<div class="container-fluid">

    <h3>League Reports</h3>

    <div class="row g-3 align-items-end mb-3">

        <!-- Program -->
        <div class="col-auto">
            <label class="form-label">Program</label>
            <select id="programid" class="form-select">
                <?php 
                    //foreach ($programs as $id => $name) :
                    foreach ($this->programs as $program) :
                    ?>
                    <option value="<?php echo (int) $program->id; ?>">
                        <?php echo htmlspecialchars($program->name, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Report -->
        <div class="col-auto">
            <label class="form-label">Select Report</label>
            <select id="viewmode" class="form-select">
                <option value="noroster">Teams with NO ROSTER</option>
            </select>
        </div>

        <!-- Refresh -->
        <div class="col-auto">
            <button id="previewBtn" class="btn btn-primary">
                Preview
            </button>
        </div>

    </div>

    <div id="ajax-output" class="border rounded p-3 bg-light"></div>

</div>

<script>
document.getElementById('previewBtn').addEventListener('click', function(e) {

    e.preventDefault();

    const programid = document.getElementById('programid').value;
    const viewmode  = document.getElementById('viewmode').value;

    Joomla.request({
        url: 'index.php?option=com_jsports&task=reports.ajaxPreview&format=raw',
        method: 'POST',

        data: new URLSearchParams({
            '<?php echo $token; ?>': 1,
            'programid': programid,
            'viewmode': viewmode
        }).toString(),

        onSuccess: function(response) {
            document.getElementById('ajax-output').innerHTML = response;
        },

        onError: function(xhr) {
            console.log(xhr.responseText);
            alert('AJAX Error');
        }
    });
});
</script>
