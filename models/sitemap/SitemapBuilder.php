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

        if (!$node instanceof \eZContentObjectTreeNode || !$node->canRead())
            return null;

        $classIdentifier = $node->classIdentifier();
        $nodeId = $node->NodeID;
        $sectionId = $node->object()->attribute('section_id');
#	print $sectionId;
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

        foreach($node->children() AS $childNode)
        {
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

        foreach($list AS $item)
        {
            $url = $doc->createElement('url');

            $loc = $doc->createElement('loc', $item['url']);
            $url->appendChild($loc);

            $lastmod = $doc->createElement('lastmod', date("c",$item['modified']));
            $url->appendChild($lastmod);

            $urlset->appendChild($url);
        }



        $doc->appendChild($urlset);

        return $doc;

    }


}

