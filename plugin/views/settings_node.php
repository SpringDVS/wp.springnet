<?php defined( 'ABSPATH' ) or die( 'Error' ); ?>
<div class="notice notice-error" id="error-banner" style="display:none;"></div>

<table class="form-table">
  <tr>
    <th>Registration Status</th>
    <td id="info-node-registered">
    <?php echo $is_registered ? 'Registered' : 'Not Registered'; ?>
    </td>
  </tr>
  <tr>
    <th>Node Status</th>
    <td id="info-node-status">
    <?php echo $is_enabled ? 'Online' : 'Offline'; ?>
    </td>
  </tr>
</table>

<?php if(!$is_registered): ?>
<button disabled>Bring Online</button>
<button id="node-register-button">Register Node</button>
<?php elseif (!$is_enabled): ?>
<button id="node-state-enable">Bring Online</button>
<button disabled>Register Node</button>
<?php elseif ($is_enabled): ?>
<button id="node-state-disable">Bring Offline</button>
<button disabled>Register Node</button>
<?php endif; ?>

