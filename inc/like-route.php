<?php

add_action( "rest_api_init", "universityLikeRoutes" );

function universityLikeRoutes() {
	register_rest_route( "university/v1", "manageLike", [
		"methods"  => "POST",
		"callback" => "createLike",
	] );

	register_rest_route( "university/v1", "manageLike", [
		"methods"  => "DELETE",
		"callback" => "deleteLike",
	] );
}

function createLike( $data ) {
	if ( is_user_logged_in() ) {
		$professor = sanitize_text_field( $data["professorId"] );

		$existQuery = new WP_Query( [
			"author"     => get_current_user_id(),
			"post_type"  => "like",
			"meta_query" => [
				[
					"key"     => "liked_professor_id",
					"compare" => "=",
					"value"   => $professor,
				],
			],
		] );

		if ( $existQuery->found_posts == 0 and get_post_type( $professor ) == "professor" ) {
			// Create new like post
			return wp_insert_post( [
				"post_type"   => "like",
				"post_status" => "publish",
				"post_title"  => "Ooooour PHP Create Post Test",
				"meta_input"  => [
					"liked_professor_id" => $professor,
				],
			] );
		} else {
			die( "Invalid professor id" );
		}
	} else {
		die( "Only loggen in users can create a like." );
	}
}

function deleteLike( $data ) {
	$likeId = sanitize_text_field( $data["like"] );
	if ( get_current_user_id() == get_post_field( "post_author", $likeId ) and get_post_type( $likeId ) == "like" ) {
		wp_delete_post( $likeId, TRUE );
		return "Congrats, Like deleted!";
	} else {
		die("You don't have permission to delete this post.");
	}
}