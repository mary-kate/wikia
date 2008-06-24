{{RequestForm3|
| Wiki title = <?= $params["request_title"] ?>
| Wiki name = <?= $params["request_name"] ?>
| language = <?= $language ?> <?= $languages[$params["request_language"]] ?>
| username = <?= $username ?>
| Description = <?= $params["request_description_international"] ?>
| Description English = <?= $params["request_description_english"] ?>
| Community = <?= $params["request_community"] ?>
| Categories = <?= $params["request_category"] ?>
<?php foreach ($categories as $id => $category): ?>
| Category<?= $id ?> = <?= $category ?>
<?php endforeach ?>
| Request ID = <?= $request_id ?>
}}
[[Category:Open requests]]
