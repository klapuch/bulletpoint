<?php
namespace Bulletpoint\Model\Report;

interface Targets {
	public function iterate(): \Iterator;
}