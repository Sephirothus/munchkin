<?php
namespace common\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\mongodb\Query;
use common\models\GameDataModel;

/**
 * Cards model
 */
class CardsModel extends ActiveRecord {

	public static $deckTypes = ['doors', 'treasures'];
	private $_decks = [];

	/**
     * Определение имени таблицы
     *
     * @return string название таблицы
     */
    public static function collectionName() {
        return 'cards';
    }

    public function attributes() {
        return ['_id', 'id'];
    }

	/**
	 * Получаем все карты
	 *
	 * @return void
	 * @author 
	 **/
	public function getCards() {
		$obj = new Query();
		$data = [];
		foreach ($obj->from(self::collectionName())->where(['_id' => ['$in' => self::$deckTypes]])->all() as $row) {
			$temp = [];
			if (!isset($data[$row['_id']])) $data[$row['_id']] = [];
			foreach ($obj->from(self::collectionName())->where(['_id' => ['$in' => $row['children']]])->all() as $child) {
				$temp = array_merge($temp, array_map(function($param) {
					return (string)$param;
				}, $child['children']));
			}
			$data[$row['_id']] = array_merge($data[$row['_id']], $temp);
		}
		$this->_decks = $data;
		return $this;
	}

	/**
	 * Тасуем карты (сохраняя ключи)
	 *
	 * @return void
	 * @author 
	 **/
	public function shuffleCards($deck=false) {
		$flag = false;
		if (!$deck) {
			$flag = true;
			$deck = $this->_decks;
		}
		foreach ($deck as $type => $val) {
			shuffle($deck[$type]);
	    }
	    if ($flag) $this->_decks = $deck;
        return $deck;
	}

	/**
	 * Раздаем карты
	 *
	 * @param data ['название колоды' => 'сколько карт на руки из данной колоды']
	 * @param players массив с ID игроков
	 * @author 
	 **/
	public function dealCards($data, $players) {
		$cards = [];
		foreach ($players as $player) {
			$cur = [];
			foreach ($this->_decks as $key => $val) {
				if (isset($data[$key]) && intval($data[$key]) > 0) {
					$cur[$key] = array_splice($this->_decks[$key], 0, $data[$key]);
				}
			}
			$cards[(string)$player] = $cur;
		}
		return $cards;
	}

	/**
	 * Получаем карты по ID
	 *
	 * @return void
	 * @author 
	 **/
	public function getCardsByIds($cards) {
		$data = [];
		foreach ($cards as $card) {
			$data[$card] = $this->getCardInfo($card, ['id', 'price']);
		}
		return $data;
	}

	/**
	 * Получаем инфо о карте
	 *
	 * @return void
	 * @author 
	 **/
	public function getCardInfo($cardID, $select='*') {
		$query = new Query;
		if ($select != '*') $query->select($select);
		$info = $query->from(self::collectionName())->where(['_id' => $cardID])->one();
		$info['_id'] = (string)$info['_id'];
		return $info;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function dealOneByType($gameId, $type, $userId) {
		$game = GameDataModel::findOne(['games_id' => $gameId]);
		$decks = $game->decks;
		$handCards = $game->hand_cards;
		$card = array_shift($decks[$type]);
		$handCards[$userId][$type][] = $card;
		$game->decks = $decks;
		$game->hand_cards = $handCards;
		$game->save();
		return $card;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function setDecks($decks) {
		$this->_decks = $decks;
		return $this;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function getDecks() {
		return $this->_decks;
	}
}