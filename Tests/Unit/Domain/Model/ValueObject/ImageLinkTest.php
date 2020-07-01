<?php declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Tests\Unit\Domain\Model\ValueObject;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Bzga\BzgaBeratungsstellensuche\Domain\Model\ValueObject\ImageLink;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ImageLinkTest extends UnitTestCase
{

    /**
     * @test
     */
    public function getCorrectIdentifierFromExternalUrl()
    {
        $identifier = '13e430b77537205400cfdc56aec80fcd';
        $subject    = new ImageLink('http://www.domain.com/path/to/image/pix.php?id=' . $identifier);
        $this->assertSame($identifier, $subject->getIdentifier());
    }
}
