<?php
namespace core;

class Parser
{
	private $content;
	
	public function __construct($file)
	{
		$this->content = file_get_contents($file);
		if (!$this->content) {
			exit('Template file read failed');
		}
	}
	
	//解析普通变量
	private function parVar()
	{
		$pattern = '/\{\$([\w]+)}/';
		$repVar = preg_match($pattern, $this->content,$matches);
		if ($repVar) {
			$this->content = preg_replace($pattern, "<?php echo \$this->vars['$matches[1]']; ?>", $this->content);
		}
	}
	
	private function parIf()
	{
		
	}
	
	public function compile($parser_file)
	{
		$this->parVar();
		file_put_contents($parser_file, $this->content);
	}
}
