<?php

echo head(array('body-class' => 'exhibit-access', 'title' => 'Exhibit Access'));

$db = get_db();
$exhibitAccessTable = $db->getTable('ExhibitAccess');
unset($usersForSelect['']);
?>

<form method='POST'>

<section class='seven columns alpha'>

<?php foreach ($exhibits as $exhibit): ?>

<?php

$allowedUserRecords = $exhibitAccessTable->findBy(array('exhibit_id' => $exhibit->id));

$allowedUserIds = array();
foreach ($allowedUserRecords as $allowedUserRecord) {
    $allowedUserIds[] = $allowedUserRecord->user_id;
}

?>
<div class='exhibit-access'>
    <?php if ($exhibitImage = record_image($exhibit, 'square_thumbnail')): ?>
        <?php echo exhibit_builder_link_to_exhibit($exhibit, $exhibitImage, array('class' => 'image')); ?>
    <?php endif; ?>
    <span>
    <a href="<?php echo html_escape(exhibit_builder_exhibit_uri($exhibit)); ?>"><?php echo metadata($exhibit, 'title'); ?></a>
    <?php if(!$exhibit->public): ?>
        <?php echo __('(Private)'); ?>
    <?php endif; ?>
    </span>
    <div class='users-select'>
        <?php
            echo $this->formLabel('exhibits[' . $exhibit->id  . ']', __('Users With Access'));
            echo $this->formSelect('exhibits[' . $exhibit->id  . ']',
                                 $allowedUserIds,
                                 array('multiple' => true, 'size' => 10),
                                 $usersForSelect
            );
        ?>

    </div>
</div>


<?php endforeach; ?>

</section>

<section class="three columns omega">
    <div class="panel" id="save">
        <input type="submit" class="submit big green button" value="Save Changes" id="save-changes" name="submit">
    </div>
</section>

</form>

<?php 
echo foot();
?>
