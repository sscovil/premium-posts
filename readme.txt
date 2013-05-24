=== Premium Posts ===
Contributors: sscovil 
Tags: post, posts, premium, mark, tag, flag, conditional
Requires at least: 3.1
Tested up to: 3.5.1
Stable tag: 1.0

Adds a checkbox to the post publish metabox to mark a post as "Premium" content, and a conditional tag to use in `single.php`.

== Description ==

This simple plugin allows publishers to mark posts as "Premium" and adds the conditional tag `is_premium_post()` that can be used in the `single.php` template.  
  
A typical use case would be to display a different ad code on premium posts than standard posts.  
  
For example:  
  
`if ( is_premium_post() ) {

    // echo markup for premium ad code here  
} else {  
    // echo markup for standard ad code here  
}`

**NOTE:** You must make use of the conditional tag `is_premium_post()` somewhere in your theme, otherwise marking posts as premium will have no effect. For more information about how to use conditional tags, refer to [the Codex](http://codex.wordpress.org/Conditional_Tags "Link to Conditional Tags in the WordPress Codex").

== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

There are no settings or configuration for this plugin.

**NOTE:** You must make use of the conditional tag `is_premium_post()` somewhere in your theme, otherwise marking posts as premium will have no effect. For more information about how to use conditional tags, refer to [the Codex](http://codex.wordpress.org/Conditional_Tags "Link to Conditional Tags in the WordPress Codex").