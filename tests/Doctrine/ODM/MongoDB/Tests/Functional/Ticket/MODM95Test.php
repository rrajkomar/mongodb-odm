<?php

namespace Doctrine\ODM\MongoDB\Tests\Functional\Ticket;

use Doctrine\Common\Collections\ArrayCollection;

class MODM95Test extends \Doctrine\ODM\MongoDB\Tests\BaseTest
{
    public function testDocumentWithEmbeddedDocuments()
    {
        $embeddedDocuments = array(new MODM95TestEmbeddedDocument('foo'));

        $testDoc = new MODM95TestDocument();
        $testDoc->setEmbeddedDocuments($embeddedDocuments);
        $this->dm->persist($testDoc);
        $this->dm->flush();
        $this->dm->clear();

        $testDoc = $this->dm->findOne(__NAMESPACE__.'\MODM95TestDocument');
        $this->assertEquals($embeddedDocuments, $testDoc->embeddedDocuments->toArray());

        $this->dm->remove($testDoc);
        $this->dm->flush();
        $this->dm->clear();

        $testDocLoad = $this->dm->findOne(__NAMESPACE__.'\MODM95TestDocument');
        $this->assertNull($testDocLoad);

        $this->dm->persist($testDoc);
        $this->dm->flush();
        $this->dm->clear();

        $testDocLoad = $this->dm->findOne(__NAMESPACE__.'\MODM95TestDocument');
        $this->assertNotNull($testDocLoad);

        $this->assertEquals($embeddedDocuments, $testDocLoad->embeddedDocuments->toArray());
    }
}

/** @Document */
class MODM95TestDocument
{
    /** @Id */
    public $id;

    // Note: Test case fails with default "pushAll" strategy, but "set" works
    /** @EmbedMany(targetDocument="MODM95TestEmbeddedDocument") */
    public $embeddedDocuments;

    public function __construct() {
        $this->embeddedDocuments = new ArrayCollection();
    }

    /**
     * Sets children
     *
     * If $images is not an array or Traversable object, this method will simply
     * clear the images collection property.  If any elements in the parameter
     * are not an Image object, this method will attempt to convert them to one
     * by mapping array indexes (size URL's are required, cropMetadata is not).
     * Any invalid elements will be ignored.
     *
     * @param array|Traversable $children
     */
    public function setEmbeddedDocuments($embeddedDocuments) {
        $this->embeddedDocuments->clear();

        if (! (is_array($embeddedDocuments) || $embeddedDocuments instanceof \Traversable)) {
            return;
        }

        foreach ($embeddedDocuments as $embeddedDocument) {
            $this->embeddedDocuments->add($embeddedDocument);
        }
    }
}

/** @EmbeddedDocument */
class MODM95TestEmbeddedDocument
{
    /** @String */
    public $name;

    public function __construct($name) {
        $this->name = $name;
    }
}