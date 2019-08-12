
<?php echo $this->Html->script('https://www.google.com/recaptcha/api.js?render='.$google_recaptcha['public_key'], ['block' => true]); ?>

<?php $action = !empty($action) ? $action : 'app' ?>

<script>
<?php $this->Html->scriptStart(['block' => true]); ?>
grecaptcha.ready(function() {
  grecaptcha.execute('<?= $google_recaptcha['public_key'] ?>', {action: '<?= $action ?>'})
    .then(function(token) {
      var recaptchaResponse = document.getElementById('<?= $google_recaptcha['input_name'] ?>');
      recaptchaResponse.value = token;
    });
});
<?php $this->Html->scriptEnd(); ?>
</script>
