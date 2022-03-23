<?php $__env->startComponent('mail::message'); ?>
Hello **<?php echo e($name); ?>**,  
Thank you for signing up on **Tweeter**.

Click below to **verify** your account

<form method="POST" action=<?php echo e($link); ?>>
<?php echo method_field('PUT'); ?>
    <input
        type="submit"
        value="Verify"
    >
</form>

## Have fun.

Sincerly,
Marco
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\onBoarding-TweeterAPI\resources\views/mails/verifcation.blade.php ENDPATH**/ ?>