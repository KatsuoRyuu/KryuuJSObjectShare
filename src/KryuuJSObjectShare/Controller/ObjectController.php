<?php

namespace KryuuJSObjectShare\Controller;

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
 * @version 20140730 
 * @link https://github.com/KatsuoRyuu/
 */

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ObjectController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
	
	public function jsAction()
	{
		$viewModel = new ViewModel();
		$viewModel->setTerminal(TRUE);
		
		$em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$objects = $em->getRepository('KryuuJSObjectShare\Entity\Object')->findBy(array('sessionID'=>$this->params('id')));
		
		$objectsArray = array();
		foreach ($objects as $object)
		{
			$objectsArray[$object->name][] = $object->object;
			if ($this->params('secure') == 1)
			{
				$em->remove($object);
			}
		}
		$em->flush();
		
		$this->removeTimeOuts($em);
		
		$viewModel->setVariable('shareObjects', $objectsArray);
		
		return $viewModel;
		
	}
	
	public function loadAction()
	{
		$request = $this->getRequest();
		if ($request->isXmlHttpRequest()) 
        {
			
		}
	}
    
    public function saveAction()
    {
        
    }
	
	public function testAction()
	{
		
		$viewModel = new ViewModel();
		
		$object = $this->getServiceLocator()->get('kryuu_share_object');
		
		$testObject = new \stdClass();
		$testObject->username = 'someusername';
		$testObject->password = 'sldkfhsdfouhwqe';
		$testObjectAddress = new \stdClass();
		$testObjectAddress->address = 'some street';
		$testObjectAddress->zip = '123-1234';
		$testObjectAddress->country = 'Japan';
		$testObject->address = $testObjectAddress;
		
		$object->share($testObject);
		
		$testObject = new \stdClass();
		$testObject->username = 'some Other Username';
		$testObject->password = 'sldkfhsdfouhwqe';
		$testObjectAddress = new \stdClass();
		$testObjectAddress->address = 'some Other street';
		$testObjectAddress->zip = '321-4321';
		$testObjectAddress->country = 'Japan';
		$testObject->address = $testObjectAddress;
		
		$object->share($testObject);
		
		$sessionIdServiceTest = $this->getServiceLocator()->get('kryuu_share_object');
		
		$viewModel->setVariable('sessionID', $sessionIdServiceTest->getSessionID());
		$viewModel->setVariable('secure',1);
		
		return $viewModel;
	}
	
	private function removeTimeOuts($em)
	{
		$queryBuilder = $em->createQueryBuilder();
		
		$query = $queryBuilder->select('u')
			->from('KryuuJSObjectShare\Entity\Object', 'u')
			->where('u.timeOut < :time')
			->setParameter('time',time())
			->getQuery();		
		
		$timeOutObjects = $query->getResult();
		
		foreach ($timeOutObjects as $object)
		{
			$em->remove($object);
		}
		$em->flush();
	}
}
