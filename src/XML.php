<?php
declare(strict_types = 1);

namespace Assertis\Util;

use DOMDocument;
use DOMElement;
use RuntimeException;
use SimpleXMLElement;
use UnexpectedValueException;

/**
 * Adds functionality to PHP's SimpleXMLElement.
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class XML extends SimpleXMLElement
{
    /**
     * @param $filename
     * @param string $class
     * @return XML
     */
    public static function load(string $filename, string $class = __CLASS__): XML
    {
        return simplexml_load_file($filename, $class);
    }

    /**
     * Copies all attributes from object into self.
     *
     * @param SimpleXMLElement $from
     * @param bool $overwrite
     * @return self
     */
    public function copyAttributes(SimpleXMLElement $from, bool $overwrite = false): XML
    {
        foreach ($from->attributes() as $kk => $vv) {
            if ($overwrite || !isset($this[$kk])) {
                $this[$kk] = (string)$vv;
            }
        }

        return $this;
    }

    /**
     * @param SimpleXMLElement $from
     * @param bool $overwrite
     * @return self
     */
    public function copyNodes(SimpleXMLElement $from, bool $overwrite = false): XML
    {
        foreach ($from->children() as $nodeName => $node) {
            $oldNode = $this->find($nodeName);
            if ($oldNode) {
                if ($overwrite) {
                    $oldNode->replace($node);
                } else {
                    $oldNode->copyNodes($node);
                }
            } else {
                $this->append($node);
            }
        }

        return $this;
    }

    /**
     * Adds an existing SimpleXMLElement to this tree.
     *
     * @param SimpleXMLElement $newChild An element to add as a child.
     * @return self
     */
    public function append(SimpleXMLElement $newChild): XML
    {
        $dom = dom_import_simplexml($this);
        $newDom = dom_import_simplexml($newChild);
        $newNode = $dom->ownerDocument->importNode($newDom, true);
        $dom->appendChild($newNode);

        return $this;
    }

    /**
     * @param string $text
     * @return self
     */
    public function appendComment(string $text): XML
    {
        $dom = dom_import_simplexml($this);
        $owner = $dom->ownerDocument;
        $node = $owner->createComment($text);
        $owner->importNode($node);
        $dom->appendChild($node);

        return $this;
    }

    /**
     * Removes an element from the document.
     *
     * @return self
     */
    public function remove(): XML
    {
        $dom = dom_import_simplexml($this);
        $dom->parentNode->removeChild($dom);

        return $this;
    }

    /**
     * Replaces an element with another.
     *
     * @param SimpleXMLElement $new_child
     * @return self
     */
    public function replace(SimpleXMLElement $new_child): XML
    {
        $dom = dom_import_simplexml($this);
        $new_dom = dom_import_simplexml($new_child);
        $new_node = $dom->ownerDocument->importNode($new_dom, true);
        $dom->parentNode->replaceChild($new_node, $dom);

        return $this;
    }

    /**
     * Removes all children from a DOMElement
     * @param DOMElement $node
     */
    private function removeDOMChildren(DOMElement $node)
    {
        while ($node->firstChild) {
            while ($node->firstChild->firstChild) {
                self::removeDOMChildren($node->firstChild);
            }
            $node->removeChild($node->firstChild);
        }
    }

    /**
     * Creates a CDATA section element to this tree.
     *
     * @param string $name Node name
     * @param string $text String to append.
     * @return XML
     * @throws RuntimeException
     */
    public function cdata(string $name, string $text = null): XML
    {
        if ($text === null && !$this->getName()) {
            throw new RuntimeException("Element not initialized yet, can't add CDATA.");
        }

        if ($text === null) {
            $obj = $this;
            $text = $name;
        } else {
            $obj = $this->addChild($name);
        }

        $node = dom_import_simplexml($obj);
        if (!$node) {
            throw new RuntimeException("Node does not exist.");
        }

        $this->removeDOMChildren($node);
        $owner = $node->ownerDocument;
        $node->appendChild($owner->createCDATASection($text));

        return $obj;
    }

    /**
     * Returns an SimpleXMLElement attribute value.
     *
     * @param string $name
     * @param mixed $value
     * @return string Attribute value.
     */
    public function attr(string $name, $value = null)
    {
        if (func_num_args() === 2) {
            return (string)($this[$name] = $value);
        } else {
            return (string)$this[$name];
        }
    }

    /**
     * Searches for an XPath in this tree and returns the first matching
     * element or null.
     *
     * @param string $path XPath to search for.
     * @param bool $strict Throw an exception if more than one element matches the XPath
     * @return XML|null The object matching the $xpath or null.
     */
    public function find(string $path, bool $strict = false)
    {
        if (false === $strict) {
            trigger_error('Use of non-strict mode in XML::find is deprecated', E_USER_DEPRECATED);
        }
        
        $tmp = $this->xpath($path);

        if ($strict && count($tmp) > 1) {
            throw new UnexpectedValueException(sprintf(
                'Found %d nodes matching path %s',
                count($tmp),
                $path
            ));
        }

        return isset($tmp[0]) ? $tmp[0] : null;
    }

    /**
     * This is a helper for XML files that have a default unprefixed namespace (i.e. `<Foo xmlns="...">`.
     * SimpleXMLElement requires you to register that namespace before each call to XML::xpath().
     *
     * @param string $newPrefix
     * @param string $path
     * @return XML[]|SimpleXMLElement[]|false
     */
    public function xpathNs(string $newPrefix, string $path)
    {
        return $this->registerUnprefixedNamespaceAs($newPrefix)->xpath($path);
    }

    /**
     * This is the unprefixed namespace equivalent of XML::find().
     * @see XML::find()
     * @see XML::xpathNs()
     *
     * @param string $newPrefix
     * @param string $path
     * @param bool $strict
     * @return XML|null
     * @throws UnexpectedValueException
     */
    public function findNs(string $newPrefix, string $path, bool $strict = false)
    {
        if (false === $strict) {
            trigger_error('Use of non-strict mode in XML::findNs is deprecated', E_USER_DEPRECATED);
        }

        $tmp = $this->xpathNs($newPrefix, $path);

        if ($strict && count($tmp) > 1) {
            throw new UnexpectedValueException(sprintf(
                'Found %d nodes matching path %s',
                count($tmp),
                $path
            ));
        }

        return isset($tmp[0]) ? $tmp[0] : null;
    }

    /**
     * @param string $newPrefix
     * @return self
     */
    private function registerUnprefixedNamespaceAs(string $newPrefix): XML
    {
        foreach ($this->getDocNamespaces() as $currentPrefix => $namespace) {
            if (strlen($currentPrefix) !== 0) {
                continue;
            }
            $this->registerXPathNamespace($newPrefix, $namespace);
        }

        return $this;
    }

    /**
     * Finds the parent of an element.
     *
     * @return static
     */
    public function parent()
    {
        return $this->find('parent::*');
    }

    /**
     * Returns or saves the XML representation of this object.
     *
     * @param bool $format
     * @param bool $preserveWhiteSpace
     * @param bool $noXmlHeader
     * @return string|false The XML string or operation result if saving.
     */
    public function asXML($format = true, $preserveWhiteSpace = false, $noXmlHeader = false)
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = $format;
        $doc->preserveWhiteSpace = $preserveWhiteSpace;
        $doc->loadXML(parent::asXML());

        return $noXmlHeader ?
            $doc->saveXML($doc->documentElement) :
            $doc->saveXML();
    }

    /**
     * Creates an HTML representation of the this XML object.
     *
     * @return string HTML string.
     */
    public function asHTML(): string
    {
        $replace = [
            ' '  => '&nbsp;',
            "\t" => '&nbsp; &nbsp; ',
            '<'  => '&lt;',
            '>'  => '&gt;',
        ];
        return nl2br(str_replace(array_keys($replace), array_values($replace), $this->asXML(true, false, true)));
    }
}
