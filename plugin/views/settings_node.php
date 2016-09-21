<?php defined( 'ABSPATH' ) or die( 'Error' ); ?>
<div class="notice notice-error" id="error-banner" style="display:none;"></div>

<?php if(!$has_public_cert):?>
	<div class="notice notice-error" id="error-banner">
		Node does not have a public certificate -- please use the 
		<em>Certificate</em> tab to generate one.
	</div>
<?php endif; ?>
<?php if(!$has_uri || !$has_token):?>
	<div class="notice notice-error" id="error-banner">
		Node does not have a valid network setup -- please use the 
		<em>Network</em> tab to configure.
	</div>
<?php endif; ?>
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

<?php if(!$has_public_cert || !$has_uri || !$has_token): ?>
<button disabled>Bring Online</button>
<button disabled>Register Node</button>
<?php elseif(!$is_registered): ?>
<button disabled>Bring Online</button>
<button id="node-register-button">Register Node</button>
<?php elseif (!$is_enabled): ?>
<button id="node-state-enable">Bring Online</button>
<button disabled>Register Node</button>
<?php elseif ($is_enabled): ?>
<button id="node-state-disable">Bring Offline</button>
<button disabled>Register Node</button>
<?php endif; ?>

<?php if($has_uri): ?>

<table class="form-table">
  <tr>
    <th>URI</th>
    <td id="info-node-status">
    <?php echo esc_attr(get_option('node_uri')); ?>
    </td>
  </tr>
  
  <tr>
    <th>GeoNetwork</th>
    <td id="info-node-registered">
    <?php echo esc_attr(get_option('geonet_name')); ?>.uk
    </td>
  </tr>
    
  <tr>
    <th>Primary Service</th>
    <td id="info-node-status">
    <?php echo esc_attr(get_option('geonet_hostname')); ?>
    </td>
  </tr>

  <tr>
    <th>Node Service</th>
    <td id="info-node-status">
    <?php echo esc_attr(get_option('node_hostname')); ?>
    </td>
  </tr>
</table>
<?php endif; ?>

