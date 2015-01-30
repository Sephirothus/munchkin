<?php
namespace backend\views;

use Yii;
use yii\helpers\Html;

echo Html::tag('div', 
	Html::tag('div', 
		Html::tag('div', 
			'', 
			['class' => 'col-md-4 js_hand_cards']
		).Html::tag('div', 
			'', 
			['class' => 'col-md-8 js_first_row', 'style' => 'height:100px;']
		), 
		['class' => 'row']
	).Html::tag('div', 
		Html::tag('div', 
			'', 
			['class' => 'col-md-12 js_second_row']
		).Html::tag('div', 
			Html::tag('span', $player['name'].' '.Html::tag('span', '1 lvl', ['id' => 'lvl']).' '.Html::tag('span', '('.$player['sex'].')', ['id' => 'sex']), ['class' => 'label label-primary']),
			['class' => 'col-md-12 text-left']
		), 
		['class' => 'row']
	), 
	['class' => 'col-md-'.$width.' text-center js_players', 'id' => $playerId]
);