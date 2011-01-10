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

		$meta = get_post_meta($post_id, 'team_count');
		$this->assertEquals('1', $meta[0][0]['team_a']);
		$this->assertEquals('2', $meta[0][0]['team_b']);

		$_POST['add-new'] = 'add-new';
		$_POST['team_a'] = '3';
		$_POST['team_b'] = '4';
		$wpTournament = new wpTournaments();
		$wpTournament->wp_insert_post($post_id, $post);

		$meta = get_post_meta($post_id, 'team_count');
		$this->assertEquals('1', $meta[0][0]['team_a']);
		$this->assertEquals('2', $meta[0][0]['team_b']);
		$this->assertEquals('3', $meta[0][1]['team_a']);
		$this->assertEquals('4', $meta[0][1]['team_b']);

	}
}
