<?
require('makepdf/fpdf.php');

// fpdf extensions
class PDF extends FPDF {

	protected $B = 0;
	protected $I = 0;
	protected $U = 0;
	protected $HREF = '';

/*
  function PDF($orientation='P',$unit='mm',$format='A4') {
      //Call parent constructor
      $this->__construct($orientation,$unit,$format);
      //Initialization
      $this->B=0;
      $this->I=0;
      $this->U=0;
      $this->HREF='';
  }
*/

  function WriteHTML($html,$lh) {
    //HTML parser
    $html=str_replace("\n",' ',$html);
    $html=str_replace("\t",' ',$html);
    $html=str_replace('<P>',"\n",$html);
    $html=str_replace('</P>',' ',$html);
    $html=str_replace('  ',' ',$html);
    $html=str_replace('  ',' ',$html);
    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	foreach($a as $i=>$e)
	{
		if($i%2==0)
		{
			// Text
			if($this->HREF)
				$this->PutLink($this->HREF,$e);
			else
				$this->Write(5,$e);
		}
		else
		{
			// Tag
			if($e[0]=='/')
				$this->CloseTag(strtoupper(substr($e,1)));
			else
			{
				// Extract attributes
				$a2 = explode(' ',$e);
				$tag = strtoupper(array_shift($a2));
				$attr = array();
				foreach($a2 as $v)
				{
					if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
						$attr[strtoupper($a3[1])] = $a3[2];
				}
				$this->OpenTag($tag,$attr);
			}
		}
	}
  }

  function OpenTag($tag,$attr) {
      //Opening tag
      if($tag=='B' or $tag=='I' or $tag=='U')
          $this->SetStyle($tag,true);
      if($tag=='A')
          $this->HREF=$attr['HREF'];
      if($tag=='BR')
          $this->Ln(5);
  }

  function CloseTag($tag) {
      //Closing tag
      if($tag=='B' or $tag=='I' or $tag=='U')
          $this->SetStyle($tag,false);
      if($tag=='A')
          $this->HREF='';
  }

  function SetStyle($tag,$enable) {
      //Modify style and select corresponding font
      $this->$tag+=($enable ? 1 : -1);
      $style='';
      foreach(array('B','I','U') as $s)
          if($this->$s>0)
              $style.=$s;
      $this->SetFont('',$style);
  }

  function PutLink($URL,$txt,$lh) {
      //Put a hyperlink
      $this->SetTextColor(0,0,255);
      $this->SetStyle('U',true);
      $this->Write($lh,$txt,$URL);
      $this->SetStyle('U',false);
      $this->SetTextColor(0);
  }

  function Header() {
  }

  function Footer() {
  }

  function Cbr(){
    $cy = $this->GetY();
    if($cy>260) {
      $this->AddPage();
    }
  }

}
