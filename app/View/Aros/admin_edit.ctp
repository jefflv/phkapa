<?php
$this->html->script('aro_add_edit', false);
$empty = __('Type Group');
if ($this->Form->value('Aro.foreign_key')){
   $empty=null; 
};
?>

<h2 class="grid_16" id="page-heading"><?php echo __('Edit %s', __('Aro')); ?></h2>
<div class="grid_16 actionsContainer">
    <div class="grid_4" id="actions">	

        <h2>
            <a href="#" id="toggle-admin-actions"><?php echo __('Menu'); ?></a>
        </h2>
        <div class="block" id="admin-actions">			
            <h5><?php echo __('Aro'); ?></h5>
            <ul class="menu">
                <li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('Aro.id')), array('confirm'=>__('Are you sure you want to delete # %s?', $this->Form->value('Aro.alias')) .'. '.__('This will also delete childs!!'))); ?></li>
                <li><?php echo $this->Html->link(__('List %s', __n('Aro','Aros',2)), array('action' => 'index')); ?></li>
            </ul>


        </div>

    </div>



    <div class="origins form">
        <?php echo $this->Form->create('Aro'); ?>
        <fieldset class="ui-corner-all ui-widget-content" >
            <legend><?php echo __('Record').' '. __('Aro'); ?></legend>
            <?php
            echo $this->Form->input('id');
            echo $this->Form->input('foreign_key', array('options'=>$foreignKeys, 'label' => __('Group / User'), 'empty' => $empty));
            echo $this->Form->input('alias', array('label' => __('Name')));
            echo $this->Form->input('parent_id',array('label' => __('Parent node')));
            ?>
        </fieldset>
        <?php echo $this->Form->end(__('Submit')); ?>
    </div><?php
        echo "<ul>";

        foreach ($acolist as $key => $value) {
            $allow = $this->html->link(__("Allow"), array('action' => 'allow', $this->Form->value('Aro.id'), str_replace("/", "*", $acoAliasList[$key]), $this->Form->value('Aro.alias')));
            $deny = $this->html->link(__("Deny"), array('action' => 'deny', $this->Form->value('Aro.id'), str_replace("/", "*", $acoAliasList[$key]), $this->Form->value('Aro.alias')));
            //$remove = $this->html->link("Remove", array('action' => 'remove', $this->Form->value('Aro.id'), $acoAliasList[$key], $this->Form->value('Aro.alias')));
            $valueTmp=trim($value, " -..- ");
            $value=  str_replace($valueTmp, __($valueTmp), $value);
            $status = "";
            $color = "";
            $permission = $acoAccessList[$key];
            $permission == '1' ? $status = __("Allowed") : $status = __("Denyed");
            $permission == '1' ? $color = "green" : $color = "red";
            echo "<li>[ $allow | $deny ] <span class='$color'> ". $value ." [ $status ]</span></li>";
            
        }
        echo "</ul>";
        ?>
</div>
<div class="clear"></div>
