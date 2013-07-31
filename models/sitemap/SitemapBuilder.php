<?php
namespace sitemap\models\sitemap;

class SitemapBuilder {
    /**
     * @static
     * @param \eZContentObjectTreeNode $node
     * @param array $excludeCfg
     * @return array|null
     */
    public static function buildNodeList(\eZContentObjectTreeNode $node, $excludeCfg = array())
    {
        static $excludes;

        if (!isset($excludes))
            $excludes = $excludeCfg;

        if (!$node instanceof \eZContentObjectTreeNode)
            return array();
        if (!$node->attribute('contentobject_is_published'))
            return array();
        if (!$node->canRead())
            return array();

        $classIdentifier = $node->classIdentifier();
        $nodeId = $node->NodeID;
        $sectionId = $node->object()->attribute('section_id');

        // HANDLE EXCLUDES
        if (isset($excludes['class_identifiers']) && in_array($classIdentifier, $excludes['class_identifiers']))
            return null;
        else if (isset($excludes['nodes']) && in_array($nodeId, $excludes['nodes']))
            return null;
        elseif (isset($excludes['sections']) && in_array($sectionId, $excludes['sections']))
            return null;

        $list = array();

        $url = $node->url();
        \eZURI::transformURI($url, false, 'full');

        $item = array(
            'nodeId' => $nodeId,
            'name' => $node->Name,
            'url'  => $url,
            'modified' => $node->object()->Modified

        );

        $list[] = $item;
        foreach ($node->children() as $childNode) {
            $list = array_merge($list, self::buildNodeList($childNode));
        }

        return $list;
    }


    /**
     * @param array $list
     * @return DOMDocuemnt|DOMDocument
     */
    public static function xml(array $list)
    {
        /* @var $doc DOMDocuemnt */
        $doc = new \DOMDocument('1.0', 'UTF-8');

        $urlset = $doc->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach($list as $item) {
            $url = $doc->createElement('url');
            $url->appendChild($doc->createElement('loc', $item['url']));
            $url->appendChild($doc->createElement('lastmod', date("c",$item['modified'])));

            $urlset->appendChild($url);
        }

        $doc->appendChild($urlset);
        return $doc;

    }


}

