<div id="footer">
[
  <a href="<?=$rel?>/">Latest</a> |
  <a href="<?=$rel?>/Index/">Index</a> |
  <a href="<?=$rel?>/Oh-yeah/">Random</a>
 ]
</div>
</div>
    </div>

</div>

<? $sidebar = new Side(realpath($rel) . '/nav.xml');
   $sidebar->print_all($rel); ?>

<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-2770281-2";
urchinTracker();
</script>

</body>
</html>
