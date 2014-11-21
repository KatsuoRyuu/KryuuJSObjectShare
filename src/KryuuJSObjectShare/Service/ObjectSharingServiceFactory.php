<?php

namespace KryuuJSObjectShare\Service;

/**
 * @encoding UTF-8
 * @note *
 * @todo *
 * @package PackageName
 * @author Anders Blenstrup-Pedersen - KatsuoRyuu <anders-github@drake-development.org>
 * @license *
 * The Ryuu Technology License
 *
 * Copyright 2014 Ryuu Technology by 
 * KatsuoRyuu <anders-github@drake-development.org>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * Ryuu Technology shall be visible and readable to anyone using the software 
 * and shall be written in one of the following ways: Ryuu Technology 
 * or by using the company logo.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @version 20140506 
 * @link https://github.com/KatsuoRyuu/
 */

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use KryuuJSObjectShare\Entity\Object;

/**
 * 
 */
class ObjectSharingServiceFactory implements FactoryInterface
{
    
	private $serviceLocator = null;
	
	private $entityManager = null;
	
	private $sessionID = null;
    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->entityManager = $this->serviceLocator->get('Doctrine\ORM\EntityManager');
		$this->sessionID = uniqid();

        return $this;
    }	
	
	public function share($shareingObject)
	{
		$containerObject = new Object();
		$containerObject->__set('name', ($shareingObject instanceof stdClass) ? 'stdClass' : get_class($shareingObject) );
		$containerObject->__set('timeOut', (time()+86400));
		$containerObject->__set('sessionID', $this->sessionID);
		$containerObject->__set('object', json_encode($shareingObject));
		
		$this->entityManager->persist($containerObject);
		$this->entityManager->flush();
	}
	
	public function getUrl()
	{
		throw new Exception('Not yet implemented');
	}
	
	public function getSessionID()
	{
		return $this->sessionID;
	}
    
}
