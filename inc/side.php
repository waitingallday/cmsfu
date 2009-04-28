<?php

// @author Jason Smith
// @version 2.5
// @desc Object for display of navigation sidebar

// @object Side
// @desc Data structure and methods to read and display external xml, representing right nav
// -----------------------------------------------
class Side {
  var $block;

// @name Side
// @desc Constructor reads given xml file into local object array structures
// @param $filename xml filename (relative path where applicable)
// -----------------------------------------------
  function Side($filename) {
    $data = implode("", file($filename));
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, $data, $values, $tags);
    xml_parser_free($parser);

    foreach ($values as $k => $v) {
      if ($v['type'] == 'open' && $v['tag'] != 'side') {
        if ($v['tag'] == 'block' && isset($v['attributes']['heading'])) {
          $this->block[]['heading'] = $v['attributes']['heading'];
        } else {
          $this->block[]['heading'] = ' ';
        }
        if ($v['tag'] == 'block' && isset($v['attributes']['rootonly'])) {
          $b = count($this->block)-1;
          $this->block[$b]['rootonly'] = $v['attributes']['rootonly'];
        }
        if ($v['tag'] == 'block' && isset($v['attributes']['last'])) {
          $b = count($this->block)-1;
          $this->block[$b]['last'] = $v['attributes']['last'];
        }
        if ($v['tag'] == 'block' && isset($v['attributes']['headinglink'])) {
          $b = count($this->block)-1;
          $this->block[$b]['headinglink'] = $v['attributes']['headinglink'];
        }
        if ($v['tag'] == 'block' && isset($v['attributes']['extlink'])) {
          $b = count($this->block)-1;
          $this->block[$b]['extlink'] = $v['attributes']['extlink'];
        }

      } elseif  ($v['type'] == 'complete') {
        if ($v['tag'] == 'content') {
          $b = count($this->block)-1;
          $this->block[$b]['content'] = $v['value'];

        } elseif ($v['tag'] == 'link') {
          $b = count($this->block)-1;
          if (isset($this->block[$b]['links'])) $c = count($this->block[$b]['links']); else $c=0;
          $this->block[$b]['links'][$c]['href'] = $v['value'];
          $this->block[$b]['links'][$c]['title'] = $v['attributes']['title'];

        } elseif ($v['tag'] == 'call') {
          $b = count($this->block)-1;
          $this->block[$b]['call'] = $v['value'];
        }
      }
    } //print_a($this->block);
  }

// @name print_all
// @desc Determine path relative to root then traverse block array and display nav content
// -----------------------------------------------
  function print_all($rel) {
  //  if (file_exists('side.xml')) $rel_path = '.';
  //  else $rel_path '..';

    print<<<EOT
<div id="nav">
EOT;

    foreach ($this->block as $key => $val) {
      if (!isset($val['rootonly']) && !isset($val['last'])) {
        $this->print_blocks($val, $rel);
      }
    }

    foreach ($this->block as $key => $val) {
      if (!isset($_GET['idx']) && !isset($_GET['toc']) && $rel == '.' && isset($val['rootonly']) && !!isset($val['last'])) {
        $this->print_blocks($val, $rel);
      }
    }

    foreach ($this->block as $key => $val) {
      if (!isset($_GET['idx']) && !isset($_GET['toc']) && $rel == '.' && isset($val['rootonly']) && isset($val['last'])) {
        $this->print_blocks($val, $rel);
      }
    }

    foreach ($this->block as $key => $val) {
      if (!isset($val['rootonly']) && isset($val['last'])) {
        $this->print_blocks($val, $rel);
      }
    }

    print<<<EOT
</div>

<div id="logo">
<img src="$rel/inc/b_sml.gif" alt="" />
<a href="http://waitingallday.com/nauseatingexception.php"><span style="display: none;">curator</span></a>
<!--Creative Commons License-->
<p><a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/2.5/au/">
<img alt="Creative Commons License" src="http://creativecommons.org/images/public/somerights20.png"/></a></p>
<!--/Creative Commons License-->
<!-- <rdf:RDF xmlns="http://web.resource.org/cc/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
<Work rdf:about="">
<license rdf:resource="http://creativecommons.org/licenses/by-nc-sa/2.5/au/" />
<dc:type rdf:resource="http://purl.org/dc/dcmitype/Text" />
</Work>
<License rdf:about="http://creativecommons.org/licenses/by-nc-sa/2.5/au/"><permits rdf:resource="http://web.resource.org/cc/Reproduction"/><permits rdf:resource="http://web.resource.org/cc/Distribution"/><requires rdf:resource="http://web.resource.org/cc/Notice"/><requires rdf:resource="http://web.resource.org/cc/Attribution"/><prohibits rdf:resource="http://web.resource.org/cc/CommercialUse"/><permits rdf:resource="http://web.resource.org/cc/DerivativeWorks"/><requires rdf:resource="http://web.resource.org/cc/ShareAlike"/></License>
</rdf:RDF> -->
</div>
EOT;

  }

// @name print_blocks
// @desc Output content parsed from array into a nav block
// @param @arr array to parse
// -----------------------------------------------
  function print_blocks($arr, $rel) {
$tags=new Tags(realpath($rel).'/index.xml');

    print '
<div class="navBlock">
';
      if ($arr['heading'] != ' ') {
	    print '  <h3>';
        if (isset($arr['headinglink'])) print '<a href="' . $rel . '/' . $arr['headinglink'] . '/">' . $arr['heading'] . '</a></h3>
';
        elseif (isset($arr['extlink'])) print '<a href="' . $arr['extlink'] . '">' . $arr['heading'] . '</a></h3>
';
        else print $arr['heading'] . '</h3>
';
      }

      if (isset($arr['content']))
        print $arr['content'];
      elseif (isset($arr['call']))
        eval($arr['call']);
      else {
        print '<ul>';
        for ($i=0; $i<count($arr['links']); $i++)
          print '<li><a href="' . $rel . '/' . $arr['links'][$i]['href'] . '">' . $arr['links'][$i]['title'] . '</a></li>';
        print '</ul>';
      }

      print '
</div>
';
  }

}

?>

