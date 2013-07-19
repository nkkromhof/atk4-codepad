<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadym
 * Date: 7/19/13
 * Time: 4:13 PM
 * To change this template use File | Settings | File Templates.
 */
class page_gui extends Page {
    function init() {
        parent::init();

        list($this->md_file,$junk)=explode('.',$_GET['gui']);

        if ($this->md_file == '') {
            $this->md_file = 'index';
        }

        $parser = new dflydev\markdown\MarkdownExtraParser();
        $html = $parser->transformMarkdown(file_get_contents(
            $this->api->locatePath('docs',$this->md_file.'.md')
        ));



        $html=str_replace('<pre><code>','<?Code?>',$html);
        $html=str_replace('</code></pre>','<?/Code?>',$html);
        $html=preg_replace(
            '/<p><img src="([^"]*)" alt="([^"]*)" \/><\/p>/',
            '<?Image?>\\1 \\2<?/?>',
            $html
        );


        $this->template->loadTemplateFromString($html);


        $page=$this;
        $page->template->eachTag('Code',function($a,$b) use($page){
            $page->add('romaninsh/documenting/View_Example',null,$b)->set($a,true);
        });
        $page->template->eachTag('Example',function($a,$b) use($page){
            $page->add('romaninsh/documenting/View_Example',null,$b)->set($a);
        });
        $page->template->eachTag('Image',function($a,$b) use($page){
            list($file,$title)=explode(' ',$a,2);
            $page->add('View',null,$b)
                ->setElement('image')
                ->setAttr('src',$page->api->locateURL('public','images/doc/'.dirname($page->page).'/'.$file))
                ->setAttr('alt',$title)
                ->setAttr('title',$title);
        });

        // relative links
//        $page->template->eachTag('link',
//            function(&$a,$b) use ($page) {
//                var_dump($a);
//                var_dump($b);
//                $page->template->set($b,$page->api->url($a));
//            }
//        );
    }
}