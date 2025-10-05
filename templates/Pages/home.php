<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.10.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Http\Exception\NotFoundException;

$this->disableAutoLayout();

$checkConnection = function (string $name) {
    $error = null;
    $connected = false;
    try {
        ConnectionManager::get($name)->getDriver()->connect();
        // No exception means success
        $connected = true;
    } catch (Exception $connectionError) {
        $error = $connectionError->getMessage();
        if (method_exists($connectionError, 'getAttributes')) {
            $attributes = $connectionError->getAttributes();
            if (isset($attributes['message'])) {
                $error .= '<br />' . $attributes['message'];
            }
        }
        if ($name === 'debug_kit') {
            $error = 'Try adding your current <b>top level domain</b> to the
                <a href="https://book.cakephp.org/debugkit/5/en/index.html#configuration" target="_blank">DebugKit.safeTld</a>
            config and reload.';
            if (!in_array('sqlite', \PDO::getAvailableDrivers())) {
                $error .= '<br />You need to install the PHP extension <code>pdo_sqlite</code> so DebugKit can work properly.';
            }
        }
    }

    return compact('connected', 'error');
};

if (!Configure::read('debug')) :
    throw new NotFoundException(
        'Please replace templates/Pages/home.php with your own version or re-enable debug mode.'
    );
endif;

?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        CakePHP: the rapid development PHP framework:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css(['cake']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body class="bg-gray-200 dark:bg-base-300">
    <header class="max-w-5xl mx-auto py-8">
        <div class="text-center">
            <a href="https://cakephp.org/" target="_blank" rel="noopener">
                <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" class="max-w-[300px] mx-auto mt-6 mb-10" x="0" y="0" viewBox="0 0 984.7 241.9">
                    <path d="M299.1 205.1c8.4 0 13.7-3.7 13.7-9.3 0-4.4-3.5-6.8-9.5-6.8H290l-4.3 16.1zm-3.4 21.2c8.4 0 13.7-3.5 13.7-9.4 0-4.7-4.5-7.1-10.8-7.1h-14.2l-4.4 16.5zm-9.5-42h17.5c9.1 0 14.5 4.3 14.5 10.9 0 6.7-5.1 10.9-11.7 12.3 4.6 1.4 8.3 4.4 8.3 9.7 0 8.1-7.3 13.9-19.6 13.9h-21.5zm37.8 36.8c0-1.6.4-3.5.9-5.3l5.1-19.2h5.1l-5.3 19.6c-.3 1.1-.5 2.7-.5 3.9 0 4.5 2.9 7.2 7.7 7.2 5.3 0 10.7-4.6 12.5-11.2l5.2-19.4h5.1l-9.2 34.5h-5.1l1.5-5.6c-3.1 3.5-6.8 6.3-12.1 6.3-6.5-.1-10.9-4.3-10.9-10.8m51.8-37.8h5.9l-1.5 5.7h-5.9zm-3.2 13.2h5.1l-9.3 34.5h-5.1zm22.1-14.2h5.2L386.8 231h-5.2zm37.5 28.7c0-6-4.3-10.7-10.7-10.7-8.2 0-14.4 7.6-14.4 16.4 0 6.4 4 10.5 9.7 10.5 7.7.1 15.4-7.7 15.4-16.2m-30.5 6.2c0-12.2 9.3-21.4 19.4-21.4 7.1 0 11.1 4 12.9 8.5l5.9-22h5.1L432 231h-5.1l1.5-5.9c-3.3 3.7-7.5 6.7-13 6.7-7.9 0-13.7-5.8-13.7-14.6m73.1-16.1h-4.7l1.2-4.5h4.7l1.1-4.1c1.8-6.9 5.9-10.2 11.8-10.2 2.7 0 5.1.5 6.9 1.5l-1.2 4.4c-2-.9-3.7-1.4-5.7-1.4-3.3 0-5.7 2-7 6.5l-.9 3.3h11.3l-1.3 4.5h-11l-8.1 30h-5.1zm40.7 16.4.7-2.4c-3-.9-6.9-1.5-11.2-1.5-6.1 0-10.4 3.3-10.4 8.1 0 3.7 2.9 6.1 7.3 6.1 5.9 0 12-4.2 13.6-10.3m-26.1 4.7c0-7.7 6.5-12.5 15.4-12.5 4.6 0 8.9.9 12.3 1.9l.2-.8c.3-1 .5-2.3.5-3.2 0-4.5-3.1-7-9.3-7-3.7 0-6.7.7-9.7 1.8l-.9-4.3c3.4-1.3 6.9-2.1 11.4-2.1 8.7 0 13.6 4 13.6 10.7 0 1.5-.3 3.1-.7 4.7L517 231h-5l1.5-5.4c-3.2 3.7-7.4 6.1-12.9 6.1-6 .1-11.2-3.3-11.2-9.5m37.5 3.6 3.3-3.3c3.7 3.6 7.9 5.1 12 5.1 3.9 0 7.6-2.3 7.6-5.7 0-2.3-1.5-3.9-6.5-5.9-6.2-2.4-9.8-4.7-9.8-9.5 0-5.9 5.1-10.6 13.1-10.6 5.1 0 9.9 2.3 12.8 4.7l-3 3.5c-2.9-2.3-6.5-4-10.2-4-4.9 0-7.5 2.6-7.5 5.5 0 2.3 1.4 3.7 6.9 5.9 6.2 2.5 9.4 5 9.4 9.5 0 6.4-5.9 10.9-13.2 10.9-5.4-.1-10.9-2.1-14.9-6.1m38.6-1c0-1.2.2-2.5.5-3.6l5.4-20.2h-4.7l1.2-4.5h4.8l2.8-10.4h5.1l-2.8 10.4h10.9l-1.2 4.5h-10.9l-5.3 19.9c-.1.8-.3 1.8-.3 2.5 0 2.4 1.8 3.5 4.7 3.5 1.5 0 3.1-.3 5-1.1l-1.3 4.7c-1.8.7-3.5 1-5.5 1-5 .1-8.4-2.2-8.4-6.7m19.3 12.1c3.9-1 5.7-2.9 6.3-5.8h-2.5l1.9-7h6.1l-1.6 6c-1.6 5.8-4.3 8.1-9.7 9.1zm75.3-26.8c0-5.1-4-9.7-10.6-9.7-7.3 0-14.2 6.1-14.2 14.4 0 6.1 4.8 9.3 10.1 9.3 7.8-.1 14.7-6.9 14.7-14m-34.8 25.8 3.2-3.6c3.7 3.1 8.3 5.2 13.6 5.2 4.3 0 7.6-1.4 9.7-3.5s3.3-4.7 4.3-8.3l.9-3.5c-3.5 3.5-7.5 6.2-13.2 6.2-7.2 0-14-4.7-14-13 0-11.5 9.7-19.6 19.4-19.6 7.4 0 10.8 4.1 12.7 8.3l2-7.5h5.1l-7.9 29.6c-1.5 5.3-3.1 8.4-5.6 10.9-3.2 3.2-7.5 4.9-13.4 4.9-6.2-.1-12.5-2.4-16.8-6.1m55.9-39.4h5.1l-2.3 9c4.2-6 9.6-10 16.4-9.6l-1.5 5.5h-.3c-7.5 0-15.1 5.5-17.8 16l-3.7 13.6H672zm50.9 14.9c0-7-4.4-11.1-10.8-11.1-7.9 0-14.8 7.9-14.8 15.9 0 7 4.4 11 10.8 11 7.8 0 14.8-7.6 14.8-15.8m-30.8 5c0-10.8 9.3-20.6 20.3-20.6 9.5 0 15.8 6.5 15.8 15.4 0 11-9.5 20.6-20.4 20.6-9.5 0-15.7-6.5-15.7-15.4m44.8-19.9h5.2l1.8 27.5 16.7-27.6h4.3L776 224l16.6-27.5h5.4l-21.5 34.8h-4.6l-2-27.2-16.6 27.2h-4.5zm71.1 29.3 3.3-3.3c3.7 3.6 7.9 5.1 12 5.1 3.9 0 7.6-2.3 7.6-5.7 0-2.3-1.5-3.9-6.5-5.9-6.2-2.4-9.7-4.7-9.7-9.5 0-5.9 5.1-10.6 13.1-10.6 5.1 0 9.9 2.3 12.7 4.7l-3 3.5c-2.9-2.3-6.5-4-10.2-4-4.9 0-7.5 2.6-7.5 5.5 0 2.3 1.4 3.7 6.9 5.9 6.2 2.5 9.4 5 9.4 9.5 0 6.4-5.9 10.9-13.2 10.9-5.3-.1-10.9-2.1-14.9-6.1m68.3-14.4c0-7-4.4-11.1-10.8-11.1-7.9 0-14.8 7.9-14.8 15.9 0 7 4.4 11 10.8 11 7.8 0 14.8-7.6 14.8-15.8m-30.8 5c0-10.8 9.3-20.6 20.3-20.6 9.5 0 15.8 6.5 15.8 15.4 0 11-9.5 20.6-20.4 20.6-9.5 0-15.7-6.5-15.7-15.4m54.7-34.1h5.1L901.4 231h-5.1zm17.7 1h5.9l-1.5 5.7h-5.9zm-3.2 13.2h5.1l-9.3 34.5h-5.1zm41.2 14.5c0-6-4.3-10.7-10.7-10.7-8.2 0-14.4 7.6-14.4 16.4 0 6.4 4 10.5 9.8 10.5 7.6.1 15.3-7.7 15.3-16.2m-30.5 6.2c0-12.2 9.3-21.4 19.4-21.4 7.1 0 11.1 4 13 8.5l5.9-22h5.1L965 231h-5.1l1.5-5.9c-3.3 3.7-7.5 6.7-13 6.7-7.9 0-13.8-5.8-13.8-14.6m44.1 6.8h6l-1.8 7h-6.2z" class="fill-[#414042] dark:fill-white"/>
                    <path d="m112.5 70.2 88 21.8c15.3-6 24.5-13.6 24.5-21.8V35.1C225 15.7 174.6 0 112.5 0 50.3 0 0 15.7 0 35.1v35.1c0 19.4 50.3 35.1 112.5 35.1zm87.9 57-88-21.9v35.1l88 21.9c15.3-6 24.5-13.6 24.5-21.9v-35.1c.1 8.3-9.1 15.9-24.5 21.9" class="fill-cake"/>
                    <path d="M0 105.3v35.1c0 19.4 50.3 35.1 112.5 35.1v-35.1C50.3 140.5 0 124.7 0 105.3" class="fill-cake"/>
                    <path d="M321.5 137.2c-27.5 0-47.9-21.2-47.9-48V89c0-26.5 20-48.3 48.7-48.3 17.6 0 28.1 5.9 36.8 14.4L346 70.2c-7.2-6.5-14.5-10.5-23.9-10.5-15.7 0-27.1 13.1-27.1 29.1v.2c0 16 11 29.3 27.1 29.3 10.7 0 17.2-4.3 24.5-10.9l13.1 13.2c-9.6 10.2-20.2 16.6-38.2 16.6m94.8-30.4c-3.5-1.6-8-2.7-12.9-2.7-8.7 0-14 3.5-14 9.9v.3c0 5.5 4.5 8.7 11.1 8.7 9.5 0 15.9-5.2 15.9-12.5v-3.7zm-.4 28.8v-7.7c-4.9 5.5-11.7 9.1-21.6 9.1-13.5 0-24.5-7.7-24.5-21.9v-.3c0-15.6 11.9-22.8 28.8-22.8 7.2 0 12.4 1.2 17.5 2.9v-1.2c0-8.4-5.2-13.1-15.3-13.1-7.7 0-13.2 1.5-19.7 3.9L376 69c7.9-3.5 15.6-5.7 27.7-5.7 22.1 0 31.9 11.5 31.9 30.8v41.5zm83 0-18.6-29.2-7.1 7.5v21.7h-20.3V38.3h20.3v51.9l23.7-26h24.3L494 92.3l28.1 43.3zm61.5-56.5c-8.4 0-13.9 6-15.5 15.2h30.5c-1.2-9.1-6.5-15.2-15-15.2m34.5 27.9h-49.7c2 9.2 8.4 14 17.5 14 6.8 0 11.7-2.1 17.3-7.3l11.6 10.3c-6.7 8.3-16.3 13.3-29.2 13.3-21.5 0-37.3-15.1-37.3-36.9v-.4c0-20.4 14.5-37.2 35.3-37.2 23.9 0 34.8 18.5 34.8 38.8v.3c0 2-.2 3.2-.3 5.1" class="fill-cake dark:fill-white"/>
                    <path d="M665.4 75c0-9.2-6.4-14.1-16.7-14.1h-15.9v28.5h16.3c10.3 0 16.3-6.2 16.3-14.1zm-16.9 32.6h-15.6v28h-20.5V42.3h38.1c22.3 0 35.7 13.2 35.7 32.3v.3c0 21.5-16.8 32.7-37.7 32.7m111.3 28V98.2h-37.9v37.4h-20.5V42.3h20.5v36.9h37.9V42.3h20.5v93.3zm96-60.6c0-9.2-6.4-14.1-16.7-14.1h-15.9v28.5h16.3c10.3 0 16.3-6.2 16.3-14.1zm-16.9 32.6h-15.6v28h-20.5V42.3h38.1c22.3 0 35.7 13.2 35.7 32.3v.3c0 21.5-16.8 32.7-37.7 32.7" class="fill-cake"/>
                </svg>
            </a>
            <h1 class="text-4xl font-raleway font-bold dark:text-white">
                Welcome to CakePHP <?= h(Configure::version()) ?> Chiffon (🍰)
            </h1>
        </div>
    </header>
    <main class="main max-w-5xl mx-auto bg-white p-6 mb-10 rounded-md shadow-md dark:bg-base-200 dark:text-gray-300">
        <div class="container">
            <div class="content">

                <div class="p-3 mb-6 bg-blue-100 text-blue-500 text-sm rounded-md dark:bg-yellow-800/30 dark:text-gray-200">
                    Please be aware that this page will not be shown if you turn off debug mode unless you replace templates/Pages/home.php with your own version.
                </div>

                <div class="mb-6">
                    <?php Debugger::checkSecurityKeys(); ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 [&_h4]:text-xl [&_h4]:mb-5 [&_ul]:ml-7 [&_li]:mb-1">
                    <div>
                        <h4 class="font-raleway">Environment</h4>
                        <ul>
                        <?php if (version_compare(PHP_VERSION, '8.1.0', '>=')) : ?>
                            <li class="bullet success">Your version of PHP is 8.1.0 or higher (detected <?= PHP_VERSION ?>).</li>
                        <?php else : ?>
                            <li class="bullet problem">Your version of PHP is too low. You need PHP 8.1.0 or higher to use CakePHP (detected <?= PHP_VERSION ?>).</li>
                        <?php endif; ?>

                        <?php if (extension_loaded('mbstring')) : ?>
                            <li class="bullet success">Your version of PHP has the mbstring extension loaded.</li>
                        <?php else : ?>
                            <li class="bullet problem">Your version of PHP does NOT have the mbstring extension loaded.</li>
                        <?php endif; ?>

                        <?php if (extension_loaded('openssl')) : ?>
                            <li class="bullet success">Your version of PHP has the openssl extension loaded.</li>
                        <?php else : ?>
                            <li class="bullet problem">Your version of PHP does NOT have the openssl extension loaded.</li>
                        <?php endif; ?>

                        <?php if (extension_loaded('intl')) : ?>
                            <li class="bullet success">Your version of PHP has the intl extension loaded.</li>
                        <?php else : ?>
                            <li class="bullet problem">Your version of PHP does NOT have the intl extension loaded.</li>
                        <?php endif; ?>

                        <?php if (ini_get('zend.assertions') !== '1') : ?>
                            <li class="bullet problem">You should set <code>zend.assertions</code> to <code>1</code> in your <code>php.ini</code> for your development environment.</li>
                        <?php endif; ?>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-raleway">Filesystem</h4>
                        <ul>
                        <?php if (is_writable(TMP)) : ?>
                            <li class="bullet success">Your tmp directory is writable.</li>
                        <?php else : ?>
                            <li class="bullet problem">Your tmp directory is NOT writable.</li>
                        <?php endif; ?>

                        <?php if (is_writable(LOGS)) : ?>
                            <li class="bullet success">Your logs directory is writable.</li>
                        <?php else : ?>
                            <li class="bullet problem">Your logs directory is NOT writable.</li>
                        <?php endif; ?>

                        <?php $settings = Cache::getConfig('_cake_translations_'); ?>
                        <?php if (!empty($settings)) : ?>
                            <li class="bullet success">The <span class="bg-gray-500 dark:bg-base-100 text-white p-1"><?= h($settings['className']) ?></span> engine is being used for core caching. To change the config edit <strong>config/app.php</strong></li>
                        <?php else : ?>
                            <li class="bullet problem">Your cache is NOT working. Please check the settings in config/app.php</li>
                        <?php endif; ?>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-raleway">Database</h4>
                        <?php
                        $result = $checkConnection('default');
                        ?>
                        <ul>
                        <?php if ($result['connected']) : ?>
                            <li class="bullet success">CakePHP is able to connect to the database.</li>
                        <?php else : ?>
                            <li class="bullet problem">CakePHP is NOT able to connect to the database.<br /><?= h($result['error']) ?></li>
                        <?php endif; ?>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-raleway">DebugKit</h4>
                        <ul>
                        <?php if (Plugin::isLoaded('DebugKit')) : ?>
                            <li class="bullet success">DebugKit is loaded.</li>
                            <?php
                            $result = $checkConnection('debug_kit');
                            ?>
                            <?php if ($result['connected']) : ?>
                                <li class="bullet success">DebugKit can connect to the database.</li>
                            <?php else : ?>
                                <li class="bullet problem">There are configuration problems present which need to be fixed:<br /><?= $result['error'] ?></li>
                            <?php endif; ?>
                        <?php else : ?>
                            <li class="bullet problem">DebugKit is <strong>not</strong> loaded.</li>
                        <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <div class="space-y-4 [&_h3]:text-2xl [&_h3]:mb-2 [&_a]:inline-block [&_a]:mr-6 [&_a]:mb-4">

                    <div class="border-t border-gray-200 dark:border-base-100 pt-6">
                        <h3 class="font-raleway">Getting Started</h3>
                        <a target="_blank" rel="noopener" href="https://book.cakephp.org/5/en/">CakePHP Documentation</a>
                        <a target="_blank" rel="noopener" href="https://book.cakephp.org/5/en/tutorials-and-examples/cms/installation.html">The 20 min CMS Tutorial</a>
                    </div>

                    <div class="border-t border-gray-200 dark:border-base-100 pt-6">
                        <h3 class="font-raleway">Help and Bug Reports</h3>
                        <a target="_blank" rel="noopener" href="https://slack-invite.cakephp.org/">Slack</a>
                        <a target="_blank" rel="noopener" href="https://github.com/cakephp/cakephp/issues">CakePHP Issues</a>
                        <a target="_blank" rel="noopener" href="https://discourse.cakephp.org/">CakePHP Forum</a>
                    </div>

                    <div class="border-t border-gray-200 dark:border-base-100 pt-6">
                        <h3 class="font-raleway">Docs and Downloads</h3>
                        <a target="_blank" rel="noopener" href="https://api.cakephp.org/">CakePHP API</a>
                        <a target="_blank" rel="noopener" href="https://bakery.cakephp.org">The Bakery</a>
                        <a target="_blank" rel="noopener" href="https://book.cakephp.org/5/en/">CakePHP Documentation</a>
                        <a target="_blank" rel="noopener" href="https://plugins.cakephp.org">CakePHP plugins repo</a>
                        <a target="_blank" rel="noopener" href="https://github.com/cakephp/">CakePHP Code</a>
                        <a target="_blank" rel="noopener" href="https://github.com/FriendsOfCake/awesome-cakephp">CakePHP Awesome List</a>
                        <a target="_blank" rel="noopener" href="https://www.cakephp.org">CakePHP</a>
                    </div>

                    <div class="border-t border-gray-200 dark:border-base-100 pt-6">
                        <h3 class="font-raleway">Training and Certification</h3>
                        <a target="_blank" rel="noopener" href="https://cakefoundation.org/">Cake Software Foundation</a>
                        <a target="_blank" rel="noopener" href="https://training.cakephp.org/">CakePHP Training</a>
                    </div>
                </div>

            </div>
        </div>
    </main>
</body>
</html>
