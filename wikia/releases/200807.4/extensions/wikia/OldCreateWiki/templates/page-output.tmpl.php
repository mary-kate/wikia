<!-- s:<?= __FILE__ ?> -->
<ol>
<li>
    Please copy &amp; paste code from below:
    <pre>
<?= $code ?>
    </pre>

    and put to page at url:
    <a href="http://www.wikia.com/index.php?title=<?= $link ?>&action=edit">
    http://www.wikia.com/index.php?title=<?= urlencode($link) ?>&amp;action=edit
    </a>.
</li>
<li>
You can check <a href="<?= $title->getFullUrl() ?>">request page</a> for this Wiki as well.
</li>
<li>
    Paste to the bottom of <a href="http://www.wikia.com/index.php?title=Template:List_of_Wikia_New&action=edit">http://www.wikia.com/index.php?title=Template:List_of_Wikia_New&amp;action=edit</a>
    <pre>
{{subst:nw|<?= $params["subdomain"] ?>|<?= $link ?>|<?= $params["language"] ?>}}
    </pre>
</li>
<li>
    Paste to <a href="http://www.wikia.com/index.php?title=New_wikis_this_week/Draft&action=edit">http://www.wikia.com/index.php?title=New_wikis_this_week/Draft&amp;action=edit</a>
    <pre>
* <?= $languages[ $params["language"] ] ?> - [[<?= $link ?>]] - http://<?= $params["subdomain"] ?>.wikia.com
    </pre>
</li>
<li>
    Paste to <a href="http://www.wikia.com/index.php?title=<?= $params["name"] ?>&action=edit">http://www.wikia.com/index.php?title=<?= $params["name"] ?>&amp;action=edit</a>
    <pre>
#Redirect [[<?= $link ?>]]
    </pre>
</li>
</ol>
<!-- e:<?= __FILE__ ?> -->
