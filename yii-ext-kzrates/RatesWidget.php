<?php
/*
 * RatesWidget  class file 
 * 
 * 
 * @version 0.5
 * @author mitrii
 * @link https://code.google.com/p/yii-ext-kzrates
 * @copyright Copyright &copy; 2010 sdek
 *
 * 
 *
 */


class RatesWidget extends CWidget{

	/**
	 * @var string the name of the container element that contains the progress bar. Defaults to 'div'.
	 */
	public $tagName = 'div';
        
	public $scriptUrl;

	public $themeUrl;

	public $theme='base';

	public $scriptFile=array(/*'rates.js'*/);
	
	public $cssFile=array('rates.css');

	public $data=array();
	
	public $options=array();

	public $htmlOptions=array();

    public $ratesToShow = array('USD', 'EUR', 'RUB');

    public $ratesURL = "http://nationalbank.kz/rss/rates_all.xml";

    public $useCache;

	public function init(){
		$this->resolvePackagePath();
		$this->registerCoreScripts();
                $this->useCache = !is_null(Yii::app()->cache);
		parent::init();
	}

	/**
	 * Run this widget.
	 * This method registers necessary javascript and renders the needed HTML code.
	 */

        public function GetRatesRSS(){
                $ch = curl_init($this->ratesURL);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                $rss = curl_exec($ch);
                curl_close($ch);

                return $rss;
        }

        public function ParseRatesRSS($rss){
                $xml = new SimpleXmlElement($rss, LIBXML_NOCDATA);
                foreach($xml->channel->item as $item)
                    if (in_array($item->title, $this->ratesToShow)) {
                        $arrRates[] = array(
                            'name'=>(string)$item->title,
                            'value'=>(string)$item->description,
                            'change'=>(string) $item->index,
                            'pubdate'=>(string) $item->pubDate,
                            );
                }
                return $arrRates;
        }

	public function run(){
		$id=$this->getId();
		$this->htmlOptions['id']=$id;
                $this->htmlOptions['class']='rates';


                if ($this->useCache) {
                    $arrRates=Yii::app()->cache->get('arrRates');
                    if($arrRates===false)
                    {
                        $rss = $this->GetRatesRSS();
                        $arrRates = $this->ParseRatesRSS($rss);
                        Yii::app()->cache->set('arrRates', $arrRates, 60*60*12); //установка кэша на 12 часов
                    }
                }
                else {
                    $rss = $this->GetRatesRSS();
                    $arrRates = $this->ParseRatesRSS($rss);
                }

                $this->render('rates', array(
                        'rates'=>$arrRates,
                        'htmlOptions'=>$this->htmlOptions,
                        'tagName'=>$this->tagName,
                    ));

		//Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$id,"$.jqplot('$id', $plotdata, $flotoptions);");
	}

	protected function resolvePackagePath(){
		if($this->scriptUrl===null || $this->themeUrl===null){
			$basePath=Yii::getPathOfAlias('application.extensions.rates.assets');
			$baseUrl=Yii::app()->getAssetManager()->publish($basePath, false, -1, true);
			if($this->scriptUrl===null)
				$this->scriptUrl=$baseUrl.'';
			if($this->themeUrl===null)
				$this->themeUrl=$baseUrl.'';
		}
	}

	protected function registerCoreScripts(){
		$cs=Yii::app()->getClientScript();
		if(is_string($this->cssFile))
			$this->registerCssFile($this->cssFile);
		else if(is_array($this->cssFile)){
			foreach($this->cssFile as $cssFile)
				$this->registerCssFile($cssFile);
		}

		$cs->registerCoreScript('jquery');
		if(is_string($this->scriptFile))
			$this->registerScriptFile($this->scriptFile);
		else if(is_array($this->scriptFile)){
			foreach($this->scriptFile as $scriptFile)
				$this->registerScriptFile($scriptFile);
		}
	}

	protected function registerScriptFile($fileName,$position=CClientScript::POS_END){
		Yii::app()->clientScript->registerScriptFile($this->scriptUrl.'/'.$fileName,$position);
	}

	protected function registerCssFile($fileName){
		Yii::app()->clientScript->registerCssFile($this->themeUrl.'/'.$fileName);
	}
}
