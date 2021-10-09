<?php

namespace block_forumdashboard\metricitems;

abstract class postcountbase extends metricitem {

  protected static abstract function getadditionaljoins();
  protected static abstract function getadditionaljoinparameters();
  protected static abstract function getadditionalconditions();
  protected static abstract function getadditionalparameters();

  private function get_joins($scope, &$conditions, &$parameters) {
    if ($scope) {
      array_push($conditions, 'discussions.course = ?');
      array_push($parameters, $scope);
      return ['JOIN {forum_discussions} discussions ON posts.discussion = discussions.id'];
    }
    return [];
  }

  public function get_value($scope, $userid) {
    global $DB;

    $conditions = [];
    $joinparameters = [];
    $parameters = [];

    $joins = array_merge($this->get_joins($scope, $conditions, $joinparameters), static::getadditionaljoins());
    $joinparameters = array_merge($joinparameters, static::getadditionaljoinparameters());

    $conditions = array_merge($conditions, ['posts.userid = ?'], static::getadditionalconditions());
    $parameters = array_merge($parameters, [$userid], static::getadditionalparameters());

    $record = $DB->get_record_sql('SELECT COUNT(*) resultcount FROM {forum_posts} posts ' . implode(' ', $joins) . ' WHERE ' . implode(' AND ', $conditions),
      array_merge($joinparameters, $parameters));
    return $record->resultcount;
  }

  public function get_average($scope) {
    global $DB;

    $conditions = [];
    $joinparameters = [];
    $parameters = [];

    $joins = array_merge($this->get_joins($scope, $conditions, $joinparameters), static::getadditionaljoins());
    $joinparameters = array_merge($joinparameters, static::getadditionaljoinparameters());

    $conditions = array_merge($conditions, static::getadditionalconditions());
    $parameters = array_merge($parameters, static::getadditionalparameters());

    $whereclause = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';

    $record = $DB->get_record_sql('SELECT AVG(p.resultcount) avgresult FROM (' .
      'SELECT COUNT(*) resultcount FROM {forum_posts} posts ' . implode(' ', $joins) . ' ' . $whereclause .
      ') p', array_merge($joinparameters, $parameters));
    return $record->avgresult;
  }

}
