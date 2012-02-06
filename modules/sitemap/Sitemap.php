<?php
namespace sitemap\modules\sitemap;

use \ezote\lib\Controller;
use \ezote\lib\Response;
use sitemap\models\sitemap\SitemapBuilder;

/**
 * @author Kristian Blom
 * @since 2011-02-05
 */
class Sitemap extends Controller
{

    /**
     * @static
     * @param int $rootNode
     * @param array $excludes
     * @return void
     */
    public static function build($rootNodeId = 2){

        $ini = \eZINI::instance('sitemap.ini');

        $excludeNodes = $ini->variable('Excludes', 'NodeList');
        $excludeClassIdentifiers = $ini->variable('Excludes', 'ClassIdentifierList');
        $excludeSections = $ini->variable('Excludes', 'SectionList');
        $excludeCfg = array(
            'nodes' => $excludeNodes,
            'class_identifiers' => $excludeClassIdentifiers,
            'sections' => $excludeSections
        );


        /* @var $node \eZContentObjectTreeNode */
        $node = \eZContentObjectTreeNode::fetch($rootNodeId);

        $tree = SitemapBuilder::buildNodeList($node, $excludeCfg);

        $doc = SitemapBuilder::xml($tree);

        $xmlText = $doc->saveXml();

        return self::response(
            $xmlText,
            array('type' => 'xml')
        );

    }

}

