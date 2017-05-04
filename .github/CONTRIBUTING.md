Thanks for joining with us. Here are a few things that you should note:

1. You'll need to git clone this repo, as we made the Smarty template system a submodule (Credits for @jolty95 for teaching me how to do so.)
2. If you are going to use JavaScript, make sure that if you can reproduce the same thing without Javascript, then do so with `<noscript>`.
3. If possible, use `echo` instead of `print` to make the code go faster.
4. If possible, use single quotes instead of double quotes to reduce memory usage.
5. If you are giving us translations, don't do it from google translate as it isn't accurate...
6. If there is any old code, don't comment it out. Instead, you should delete it.
7. If possible, use `include()` instead of `include_once` to make the code go faster.
9. Use Appropriate Str Functions: str_replace is faster than preg_replace, but strtr is faster than str_replace by a factor of 4.
10. If possible, Use Native PHP Functions.
11. Use isset() where ever possible instead of using count(), strlen(), sizeof() to check whether the value returned is greater than 0.
12. If you need to find out the time when the script started executing, $_SERVER[’REQUEST_TIME’] is preferred to time()

Here is a table of alternatives that you **should** use. We're not forcing you to use it, but its recommended...

<table>
<tr><th>Use</th><th>Instead of</th></tr>
<tr><td>''</td><td>""</td></tr>
<tr><td>echo</td><td>print</td></tr>
<tr><td>include()</td><td>include_once</td></tr>
<tr><td>strtr</td><td>str_replace</td></tr>
<tr><td>strtr</td><td>preg_replace</td></tr>
<tr><td>isset()</td><td>sizeof(), count() & strlen()</td></tr>
<tr><td>$_SERVER[’REQUEST_TIME’]</td><td>time()</td></tr>
</table>

If you follow all of these guidelines, we will review your code, and maybe accept it.
~MaorNinja.
