<?php

namespace LeadpagesWP\Admin\MetaBoxes;

use LeadpagesWP\Admin\CustomPostTypes\LeadpagesPostType;
use LeadpagesWP\models\LeadPagesPostTypeModel;
use TheLoop\Contracts\MetaBox;
use Carbon\Carbon;

class LeadpagesCreate extends LeadpagesPostType implements MetaBox
{

    /**
     * @var \LeadpagesWP\models\LeadPagesPostTypeModel
     */
    private $postTypeModel;
    /**
     * @var \Leadpages\Pages\LeadpagesPages
     */
    private $pagesApi;

    public function __construct()
    {
        global $leadpagesApp;

        $this->pagesApi      = $leadpagesApp['pagesApi'];
        $this->postTypeModel = $leadpagesApp['lpPostTypeModel'];
        $this->splitTestApi  = $leadpagesApp['splitTestApi'];
        add_action('wp_ajax_get_pages_dropdown', [$this, 'generateSelectList']);
        add_action('wp_ajax_get_pages_dropdown_nocache', [$this, 'generateSelectListNoCache']);
        add_action('wp_ajax_nopriv_get_pages_dropdown', [$this, 'generateSelectList']);
        add_action('wp_ajax_nopriv_get_pages_dropdown_nocache', [$this, 'generateSelectListNoCache']);
    }

    public static function getName()
    {
        return get_called_class();
    }

    public function defineMetaBox()
    {
        add_meta_box("leadpage-create", "Leadpages Create", [$this, 'callback'], $this->postTypeName, "normal",
          "high", null);
    }

    public function callBack($post, $box)
    {
        $useCache    = LeadPagesPostTypeModel::getMetaCache($post->ID);
        $currentType = LeadPagesPostTypeModel::getMetaPageType($post->ID);
        $slug        = LeadPagesPostTypeModel::getMetaPagePath($post->ID);
        $action      = (isset($_GET['action']) && $_GET['action'] == 'edit') ? 'Edit' : 'Add New';
        $is_edit     = $_GET['action'] == 'edit' ? 'true' : 'false';
        ?>
    <style>.select2-container--default .select2-results>.select2-results__options { max-height: 400px !important;  } </style>
    <div class="leadpages-edit-wrapper" data-is-edit="<?php echo $is_edit; ?>">
        <div id="leadpages-header-wrapper" class="flex flex--xs-between flex--xs-middle">
            <div class="ui-title-nav" aria-controls="navigation">
                <div class="ui-title-nav__img">
                    <i class="lp-icon lp-icon--alpha">leadpages_mark</i>
                </div>
                <div class="ui-title-nav__content">
                    <?php echo $action; ?> Leadpage
                </div>
            </div>

            <button id="publish" name="publish" class="ui-btn">
                Publish
                <!-- Loading icons-->
                <div class="ui-loading ui-loading--sm ui-loading--inverted">
                    <div class="ui-loading__dots ui-loading__dots--1"></div>
                    <div class="ui-loading__dots ui-loading__dots--2"></div>
                    <div class="ui-loading__dots ui-loading__dots--3"></div>
                </div>
                <!-- End Loading Icons-->
            </button>
        </div>

        <!-- Body Start -->
        <div class="leadpages-edit-body">
            <div class="flex leadpages-loading">
                <div class="ui-loading">
                    <div class="ui-loading__dots ui-loading__dots--1"></div>
                    <div class="ui-loading__dots ui-loading__dots--2"></div>
                    <div class="ui-loading__dots ui-loading__dots--3"></div>
                </div>
            </div>
            <div class="flex">
                <div class="flex__item--xs-12">
                    <p class="header_text">
                        Welcome to the Leadpages admin.  Publish a Leadpage to your site in a few easy steps below: 
                    </p>
                </div>
                <h3 class="flex__item--xs-12">Select a Leadpage</h3>
            </div>
            <div class="select_a_leadpage flex">

                <div class="leadpages_search_container flex__item--xs-7">
                    <div id="leadpages_my_selected_page"></div>
                </div>
                <div class="flex__item--xs-4" >
                <p class="flex" style="align-items: center; color: #888; margin-left: -4px;">
                    <i class="sync-leadpages lp-icon lp-icon--xsm lp-icon-sync" style="display: inline;"></i>
                    <small class="human-diff" style="padding-top: 6px; padding-bottom: 4px; padding-left: 4px; display: none;">Page listing synced: <span class="diff-message"></span>. </small>
                </p>
                </div>
            </div>


            <div class="flex">
            <div class="flex__item-xs-12">
                <p><small>Have a lot of Leadpages? Use the
                search box to quickly find your Leadpage by name.</small></p>
            </div>
            </div>

            <div class="select_a_leadpage_type flex">
                <h3 class="flex__item--xs-12">Select a Page Type</h3>

                <p class="flex__item--xs-12"> Please select a Leadpage display type below.</p>

                <div class="leadpage_type_container flex">
                    <label id="leadpage-normal-page" class="leadpage_type_box">
                        <h3 class="header">Normal Page</h3>

                        <p class="section_description">
                            This display type will allow you to direct people to this leadpage by using the
                            slug below.
                        </p>
                        <input id="leadpage-normal-page" type="radio" name="leadpages-post-type" class="leadpages-post-type leadpage-normal-page"
                               value="lp" <?php echo $currentType == "lp" ? 'checked=checked"' : ""; ?> >
                    </label>

                    <label for="leadpage-home-page" class="leadpage_type_box">
                        <h3 class="header">Home Page</h3>

                        <p>
                            This will take over your home page on your blog. Anytime someone goes to
                            your home page it will show this page.
                        </p>
                        <input id="leadpage-home-page" type="radio" name="leadpages-post-type" class="leadpages-post-type leadpage-home-page"
                               value="fp" <?php echo $currentType == "fp" ? 'checked=checked"' : ""; ?> >
                    </label>

                    <label for="leadpage-welcome-page" class="leadpage_type_box">
                        <h3 class="header">Welcome Gate &trade;</h3>

                        <p>
                            A Welcome Gate &trade; page will be the first page any new visitor to your site sees.
                        </p>
                        <input id="leadpage-welcome-page" type="radio" name="leadpages-post-type" class="leadpages-post-type leadpage-welcomegate-page"
                               value="wg" <?php echo $currentType == "wg" ? 'checked=checked"' : ""; ?> >
                    </label>

                    <label for="leadpage-404-page" class="leadpage_type_box">
                        <h3 class="header">404 Page</h3>

                        <p>
                            Set a Leadpage as your 404
                            page to ensure you are not missing out on any conversions.
                        </p>
                        <input id="leadpage-404-page" type="radio" name="leadpages-post-type" class="leadpages-post-type leadpage-404-page"
                               value="nf" <?php echo $currentType == "nf" ? 'checked=checked"' : ""; ?> >
                    </label>

                </div>
            </div>

            <div id="leadpage-slug" class="leadbox_slug flex">
                <h3 class="flex__item--xs-12">Set a Custom Slug</h3>

                <p class="flex__item--xs-12">
                    Enter a custom slug for your Leadpage. <small>This will be the url to view your Leadpage on your site.</small>
                    <br />
                </p>

                <div class="flex__item--xs-12 leadpage_slug_container">
                    <span class="lp_site_main_url"><?php echo $this->leadpages_permalink(); ?></span>
                    <input type="text" name="leadpages_slug" class="leadpages_slug_input" value="<?php echo $slug; ?>">
                </div>
            </div>
            <div id="leadpage-cache" class="leadbox_slug flex">
                <h3 class="flex__item--xs-12">Set Page Cache</h3>

                <p class="flex__item--xs-12">
                    Choose whether or not you would like to cache your page html locally.
                    This will create faster page loads, however if a page is split tested, the split tested version
                    will not load.
                </p>

                <div class="flex__item--xs-12 leadpage_cache_container">
                    <input type="radio" id="cache_this_true" name="cache_this" value="true"  <?php echo ($useCache == 'true') ? 'checked="checked"': ''; ?>> Yes, cache for improved performance. <br />
                    <input type="radio" id="cache_this_false" name="cache_this" value="false"  <?php echo ($useCache != 'true') ? 'checked="checked"': ''; ?>> No, re-fetch on each visit; slower, but required for split testing.
                </div>
            </div>
            <input type="hidden" name="leadpages_name" id="leadpages_name">
            <input type="hidden" name="leadpage_type" id="leadpageType">
        </div>
        <div id="leadpages-footer-wrapper" class="flex flex--xs-end flex--xs-middle">

            <button id="publish" name="publish" class="ui-btn">
                Publish
                <!-- Loading icons-->
                <div class="ui-loading ui-loading--sm ui-loading--inverted">
                    <div class="ui-loading__dots ui-loading__dots--1"></div>
                    <div class="ui-loading__dots ui-loading__dots--2"></div>
                    <div class="ui-loading__dots ui-loading__dots--3"></div>
                </div>
                <!-- End Loading Icons-->
            </button>
        </div>
        <?php
    }

    public function registerMetaBox()
    {
        add_action('add_meta_boxes', [$this, 'defineMetaBox']);
    }

    /**
     * Helper for wp ajax action to refresh pages w/o using cache
     */
    public function generateSelectListNoCache()
    {
        $this->generateSelectList(true);
    }

    public function generateSelectList($refresh_cache = false)
    {
        global $leadpagesApp;

        $id = sanitize_text_field($_POST['id']);
        $currentPage = LeadPagesPostTypeModel::getMetaPageId($id);

        if (!$currentPage) {
            $currentPage = $leadpagesApp['lpPostTypeModel']->getPageByXORId($id);
        }

        $pages = $this->fetchPages($refresh_cache);
        $cached_at = $pages['timestamp'];
        $human_diff = $pages['time_since'];
        $splitTest = $leadpagesApp['splitTestApi']->getActiveSplitTests();
        $items['_items'] = array_merge($pages['_items'], $splitTest);
        $items = $leadpagesApp['pagesApi']->sortPages($items);
        $size = count($items['_items']);
        $optionString = '<select data-human-diff="' . $human_diff . '" data-timestamp="'. $cached_at . '" id="select_leadpages" class="leadpage_select_dropdown" name="leadpages_my_selected_page">';
        foreach ($items['_items'] as $page) {
            if (isset($page['splitTestId'])) {
                continue;
            }

            $pageId = number_format($page['id'], 0, '.', '');
            $is_split = 'false';
            $variations = 1;
            if (isset($page['_meta']['lastUpdated'])) {
                $last_published = date('Y-m-d', $page['updated']);
                $slug = $page['slug'];
            } else {
                $is_split = 'true';
                $last_published_at = $page['_meta']['updated'];
                $last_published = date('Y-m-d', strtotime($last_published_at));
                $url_parts = parse_url($page['_meta']['controlUrl']);
                $slug = str_replace('/', '', $url_parts['path']);
                $variations = $page['_meta']['variationsCount'];
            }

            $xor_hex_id = $page['xor_hex_id'];
            $edit_url = $page['editUrl'];
            $preview_url = $page['previewUrl'];
            $publish_url = $page['publishUrl'];
            $optins = $page['optins'];
            $views = $page['views'];
            $optionString .= "
                <option data-slug='{$slug}'
                        data-issplit='{$is_split}'
                        data-variations='{$variations}'
                        data-published='{$last_published}'
                        data-optins='{$optins}'
                        data-views='{$views}'
                        data-preview-url='{$preview_url}'
                        data-publish-url='{$publish_url}'
                        data-edit-url='{$edit_url}'
                        value='{$xor_hex_id}:{$pageId}'"
                . ($currentPage == $pageId ? ' selected="selected"' : '')
                .">{$page['name']}</option>";
        }

        $optionString .= '</select>';
        echo $optionString;
        die();
    }

    protected function fetchPages($refresh_cache = false)
    {
        if ($refresh_cache) {
            $this->clearPagesCache();
        }

        if (false === ($pages = get_transient('user_leadpages'))) {
            global $leadpagesApp;
            $pages = $leadpagesApp['pagesApi']->getAllUserPages();
            $pages['timestamp'] = Carbon::now();
            set_transient('user_leadpages', $pages, 900);
        }

        $pages['time_since'] = 'just now';
        if (isset($pages['timestamp'])) {
            $pages['time_since'] = Carbon::parse($pages['timestamp'])->diffForHumans();
        }

        return $pages;
    }

    protected function clearPagesCache()
    {
        delete_transient('user_leadpages');
        return $this;
    }

    //replace with get_permalink
    public function leadpages_permalink()
    {
        global $post;
        $permalink = home_url() .'/';
        if ($post->post_status != 'publish') {
            $permalink = 'Publish to see full url';
        }
        $permalink = str_replace('/leadpages_post/', '', $permalink);
        $permalink = str_replace('/'.$post->post_name.'/', '/', $permalink);
        return $permalink;
    }

}
