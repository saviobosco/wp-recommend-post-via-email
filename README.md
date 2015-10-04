## WP Recommend Post via Email

The plugin outputs a button on each post. Once clicked on the button it wiill show popup where the user can enter friends email.

Use this plugin as shortcode. It will output the button like this
```
[dgrve]
```

Or use it in the loop or different place using the function. You need to pass a post_id if you are using it outside of the loop. You can use with or without post ID in the loop. It will be even safer if post id is passed.
```
DGRVE::recommendToFriend($post_id)
```