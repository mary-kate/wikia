{{WikiTemplate|
| wiki title = <?= $params["title"]."\n" ?>
| request name = <?= $params["subdomain"]."\n" ?>
| wiki url = http://<?= $params["subdomain"] ?>.wikia.com
| wiki logo = http://images.wikia.com/<?= $params["dir_part"] ?>/images/b/bc/Wiki.png
| request description = <?= $description."\n" ?>
| description in english = <?= $descriptionen."\n" ?>
| request created = <?=  $timestamp."\n" ?>
| request category = <?= $category."\n" ?>
<?php
    if( is_array($categories) ) {
        foreach( $categories as $id => $cat ) {
            echo "| category{$id} = {$cat}\n";
        }
    }
?>
| request id = <?= $id."\n" ?>
| username = <?= $founder->getName()."\n" ?>
| language = <?= $params["language"]."\n" ?>
| language name = <?= $languages[ $params["language"] ]."\n" ?>
}}
