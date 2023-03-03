<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace block_forumdashboard_engagement;

defined('MOODLE_INTERNAL') || die;

/**
 * Class for engagement calculation methods
 */
class engagement {
    private const COMPONENT = 'block_forumdashboard';
    public const PERSON_TO_PERSON = 1;
    public const THREAD_TOTAL_COUNT = 2;
    public const THREAD_ENGAGEMENT = 3;

    /**
     * Get string of calculation method
     *
     * @param string $method
     * @param string $suffix
     * @return string
     */
    private static function getstring($method, $suffix = '') {
        switch ($method) {
            case static::PERSON_TO_PERSON: return get_string('engagement_persontoperson' . $suffix, static::COMPONENT);
            case static::THREAD_TOTAL_COUNT: return get_string('engagement_threadtotalcount' . $suffix, static::COMPONENT);
            case static::THREAD_ENGAGEMENT: return get_string('engagement_threadengagement' . $suffix, static::COMPONENT);
        }
        throw new \moodle_exception('Invalid method');
    }

    /**
     * Get calculator function
     *
     * @param int $method
     * @param int $discussionid
     * @param int $starttime
     * @param int $endtime
     * @return engagementcalculator
     */
    public static function getinstancefrommethod($method, $discussionid, $starttime = 0, $endtime = 0) {
        switch ($method) {
            case static::PERSON_TO_PERSON: return new p2pengagement($discussionid, $starttime, $endtime);
            case static::THREAD_TOTAL_COUNT: return new threadcountengagement($discussionid, $starttime, $endtime);
            case static::THREAD_ENGAGEMENT: return new threadengagement($discussionid, $starttime, $endtime);
        }
        throw new \moodle_exception('Invalid method');
    }

    /**
     * Get calculation method name
     *
     * @param string $method
     * @return string
     */
    public static function getname($method) {
        return static::getstring($method);
    }

    /**
     * Get calculation method description
     *
     * @param string $method
     * @return string
     */
    public static function getdescription($method) {
        return static::getstring($method, '_description');
    }

    /**
     * Get all available engagement calculation methods
     *
     * @return int[]
     */
    public static function getallmethods() {
        return [
            static::PERSON_TO_PERSON,
            static::THREAD_TOTAL_COUNT,
            static::THREAD_ENGAGEMENT
        ];
    }

    /**
     * Get select options for form
     *
     * @return array
     */
    public static function getselectoptions() {
        $options = [];
        foreach (static::getallmethods() as $option) {
            $options[$option] = static::getname($option);
        }
        return $options;
    }

    /**
     * Add options to form
     *
     * @param MoodleQuickForm $mform
     */
    public static function addtoform($mform, $elementname = 'engagementmethod', $defaultvalue = null) {
        $mform->addElement('select', $elementname, get_string('engagement_method', static::COMPONENT), engagement::getselectoptions());
        $mform->addHelpButton($elementname, 'engagement_method', static::COMPONENT);
        if (is_null($defaultvalue)) {
            $defaultvalue = get_config('report_discussion_metrics', 'defaultengagementmethod');
        }
        $mform->setDefault($elementname, $defaultvalue);
    }
}

/**
 * A forum post
 */
class engagedpost {
    public $id;
    public $discussion;
    public $parent;
    public $userid;
    public $created;
    /**
     * True if post satisfies time condition
     *
     * @var bool
     */
    public $satisfiestime;
    /**
     * Children posts
     *
     * @var engagedpost[]
     */
    public $children;

    public const DB_OUT_FIELDS = 'id,discussion,parent,userid,created';
}

/**
 * Engagement result
 */
class engagementresult {
    /**
     * @var int[]
     */
    public $levels = [];

    /**
     * Increase level value by given amount or default to be 1
     *
     * @param int $level
     * @param int $amount
     */
    public function increase($level, $amount = 1) {
        if (!isset($this->levels[$level])) {
            $this->levels[$level] = $amount;
            return;
        }
        $this->levels[$level] += $amount;
    }

    /**
     * Add another result to this result
     *
     * @param engagementresult $result
     */
    public function add($result) {
        foreach ($result->levels as $level => $value) {
            $this->increase($level, $value);
        }
    }

    /**
     * @param int $level
     * @return int
     */
    public function getlevel($level) {
        return isset($this->levels[$level]) ? $this->levels[$level] : 0;
    }

    /**
     * @return int
     */
    public function getl1() {
        return $this->getlevel(1);
    }
    
    /**
     * @return int
     */
    public function getl2() {
        return $this->getlevel(2);
    }
    
    /**
     * @return int
     */
    public function getl3() {
        return $this->getlevel(3);
    }
    
    /**
     * @return int
     */
    public function getl4up() {
        $sum = 0;
        foreach ($this->levels as $level => $value) {
            if ($level < 4) {
                continue;
            }
            $sum += $value;
        }
        return $sum;
    }
    
    /**
     * @return int
     */
    public function getmax() {
        return count($this->levels) > 0 ? max(array_keys($this->levels)) : null;
    }
    
    /**
     * @return double
     */
    public function getaverage () {
        $sum = 0;
        $count = 0;
        foreach ($this->levels as $level => $value) {
            $sum += $level * $value;
            $count += $value;
        }
        return $count ? round($sum / $count, 2) : null;
    }
}

/**
 * Class for calculating engagement
 */
abstract class engagementcalculator {
    /**
     * @var int
     */
    protected $discussionid;
    /**
     * Key being post ID, value beinfg engagedposts
     *
     * @var engagedpost[]
     */
    protected $postsdict = [];
    /**
     * ID of the first post
     *
     * @var int
     */
    protected $firstpost;
    /**
     * Start timestamp
     *
     * @var int
     */
    protected $starttime = 0;
    /**
     * End timestamp
     *
     * @var int
     */
    protected $endtime = 0;

    /**
     * Constructor
     *
     * @param int $discussionid
     * @param int $starttime
     * @param int $endtime
     */
    public function __construct($discussionid, $starttime = 0, $endtime = 0) {
        $this->discussionid = $discussionid;
        $this->starttime = $starttime;
        $this->endtime = $endtime;
        $this->getposts();
        $this->initchildren();
        $this->checkpoststime();
    }

    /**
     * Get user IDs participated in the discussion
     *
     * @return int[]
     */
    public function getparticipants() {
        $results = [];
        foreach ($this->postsdict as $post) {
            if (!in_array($post->userid, $results)) {
                $results[] = $post->userid;
            }
        }
        return $results;
    }

    /**
     * Get posts from database
     */
    private function getposts() {
        global $DB;
        $posts = $DB->get_records('forum_posts', ['discussion' => $this->discussionid], '', engagedpost::DB_OUT_FIELDS);
        foreach ($posts as $post) {
            $this->postsdict[$post->id] = $post;
            if (!$post->parent) {
                $this->firstpost = $post->id;
            }
        }
    }

    /**
     * Initialise children
     */
    private function initchildren() {
        foreach ($this->postsdict as $post) {
            $post->children = $this->getchildren($post);
        }
    }

    /**
     * Get children post IDs of given postid
     *
     * @param engagedpost $parentpost
     */
    private function getchildren($parentpost) {
        $results = [];
        foreach ($this->postsdict as $post) {
            if ($post->parent == $parentpost->id) {
                $results[] = $post;
            }
        }
        return $results;
    }

    /**
     * Assign satisfies time property to posts
     */
    private function checkpoststime() {
        foreach ($this->postsdict as $post) {
            $post->satisfiestime = $this->postsatisfiestime($post);
        }
    }

    /**
     * Test if given post satisfies time condition
     *
     * @param engagedpost $post
     * @return bool
     */
    private function postsatisfiestime($post) {
        return (!$this->starttime || ($post->created >= $this->starttime))
            && (!$this->endtime || ($post->created <= $this->endtime));
    }

    /**
     * @param int $userid
     * @return engagementresult
    */
    public abstract function calculate($userid);
}

/**
 * Person-to-Person Engagement
 */
class p2pengagement extends engagementcalculator {
    /**
     * @param int $userid
     * @return engagementresult
     */
    public function calculate($userid) {
        $result = new engagementresult();
        $this->travel($userid, $this->postsdict[$this->firstpost], $result);
        return $result;
    }

    /**
     * @param int $userid
     * @param engagedpost $post
     * @param engagementresult $result
     * @param int[] $userengagement
     */
    private function travel($userid, $post, $result, &$userengagement = []) {
        foreach ($post->children as $childpost) {
            if ($childpost->userid != $post->userid && $childpost->userid == $userid) {
                if (!isset($userengagement[$post->userid])) {
                    $userengagement[$post->userid] = 0;
                }
                $userengagement[$post->userid]++;
                if ($post->satisfiestime) {
                    $result->increase($userengagement[$post->userid]);
                }
            }
            $this->travel($userid, $childpost, $result, $userengagement);
        }
    }
}

/**
 * Thread Count Engagement
 */
class threadcountengagement extends engagementcalculator {
    /**
     * @param int $userid
     * @return engagementresult
     */
    public function calculate($userid) {
        $result = new engagementresult();
        $threads = $this->postsdict[$this->firstpost]->children;
        foreach ($threads as $post) {
            $countinthread = 0;
            if ($post->userid == $userid && $post->userid != $this->postsdict[$this->firstpost]->userid) {
                $countinthread++;
                if ($post->satisfiestime) {
                    $result->increase(1);
                }
            }
            $this->travel($userid, $post, $result, $countinthread);
        }
        return $result;
    }

    /**
     * @param int $userid
     * @param engagedpost $post
     * @param engagementresult $result
     * @param int $count
     */
    public function travel($userid, $post, $result, &$count) {
        foreach ($post->children as $childpost) {
            if ($childpost->userid != $post->userid && $childpost->userid == $userid) {
                $count++;
                $result->increase($count);
            }
            $this->travel($userid, $childpost, $result, $count);
        }
    }
}

/**
 * Thread Engagement
 */
class threadengagement extends engagementcalculator {
    /**
     * @param int $userid
     * @return engagementresult
     */
    public function calculate($userid) {
        $result = new engagementresult();
        $this->travel($userid, $this->postsdict[$this->firstpost], $result);
        return $result;
    }

    /**
     * @param int $userid
     * @param engagedpost $post
     * @param engagementresult $result
     * @param int $level
     */
    public function travel($userid, $post, $result, $level = 1) {
        foreach ($post->children as $childpost) {
            if ($childpost->userid != $post->userid && $childpost->userid == $userid) {
                if ($post->satisfiestime) {
                    $result->increase($level);
                }
                $this->travel($userid, $childpost, $result, $level + 1);
            } else {
                $this->travel($userid, $childpost, $result, $level);
            }
        }
    }
}
