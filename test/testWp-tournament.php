<?php

require_once('MockPress/mockpress.php');
require_once(dirname(__FILE__) . '/../wp-tournament.php');

class wpTournament_Test extends PHPUnit_Framework_TestCase {
	public function testAddRoundOne() {
		$post = new StdClass();
		$post->post_type = 'Tournaments';
		$post->ID = 1;
		$post_id = 1;
		$_POST['add-new'] = 'add-new';
		$_POST['team_a'] = '1';
		$_POST['team_b'] = '2';
		$wpTournament = new wpTournaments();
		$wpTournament->wp_insert_post($post_id, $post);

		$meta = get_post_meta($post_id, 'round_1');
		$this->assertEquals('1', $meta[0][0]['team_a']);
		$this->assertEquals('2', $meta[0][0]['team_b']);

		$_POST['add-new'] = 'add-new';
		$_POST['team_a'] = '3';
		$_POST['team_b'] = '4';
		$wpTournament = new wpTournaments();
		$wpTournament->wp_insert_post($post_id, $post);

		$meta = get_post_meta($post_id, 'round_1');
		$this->assertEquals('1', $meta[0][0]['team_a']);
		$this->assertEquals('2', $meta[0][0]['team_b']);
		$this->assertEquals('3', $meta[0][1]['team_a']);
		$this->assertEquals('4', $meta[0][1]['team_b']);
	}

	function test_choose_winner() {
		$post = new StdClass();
		$post->post_type = 'Tournaments';
		$post->ID = 1;
		$post_id = 1;

		$post_meta = array( 
			0 => array (
				0 => array( 'team_a' => '22', 'team_b' => '21', ), 
				1 => array ( 'team_a' => '18', 'team_b' => '17', ), 
				2 => array ( 'team_a' => '15', 'team_b' => '14', ), 
				3 => array ( 'team_a' => '21', 'team_b' => '20', ),
			)
		);

		add_post_meta($post_id, 'round_1', $post_meta);

		$_POST['winner_submit'] = 'Winner';
		$_POST['round'] = 1;
		$_POST['position'] = 1;
		$_POST['post_id'] = '18';

		$wpTournament = new wpTournaments();
		$wpTournament->wp_insert_post($post_id, $post);

		$meta = get_post_meta($post_id, 'round_2');
		$this->assertNull($meta[0][0]['team_a']);
		$this->assertEquals('18', $meta[0][0]['team_b']);
	}
}
