<?php

class c_actions_API extends API {

// @TODO move all validation for AJAX interface functions to separated function

    public function getFoodGroups() {
        $foodgroups = $this->getSqlArray("SELECT * FROM groups",true,'id');
        return $foodgroups;
    }


    public function getMyStats($acts) {

        $stats = array();
        $stats['prev']['cals'] = 0;

        $today = date("M d, Y",time());
        $daycount = 0;
        $cals = 0;
        $calories_from_fat = 0;
        $saturated_fat = 0;
        $trans_fat = 0;
        $protein = 0;
        $total_carbs = 0;
        $sugars = 0;
        $fiber = 0;
        $total_fat = 0;
        $cholesterol = 0;
        $sodium = 0;
        $vitamin_a = 0;
        $vitamin_c = 0;
        $iron = 0;
        $calcium = 0;
        
        foreach (array_keys($acts) as $d) {
            if ($d != $today) {

                $cals       += $acts[$d]['calories'];
                $calories_from_fat+= $acts[$d]['calories_from_fat'];
                $saturated_fat+= $acts[$d]['saturated_fat'];
                $trans_fat+= $acts[$d]['trans_fat'];
                $protein    += $acts[$d]['protein'];
                $total_carbs+= $acts[$d]['total_carbs'];
                $sugars     += $acts[$d]['sugars'];
                $fiber      += $acts[$d]['fiber'];
                $total_fat  += $acts[$d]['total_fat'];
                $cholesterol+= $acts[$d]['cholesterol'];
                $sodium     += $acts[$d]['sodium'];
                $vitamin_a  += $acts[$d]['vitamin_a'];
                $vitamin_c  += $acts[$d]['vitamin_c'];
                $iron       += $acts[$d]['iron'];
                $calcium    += $acts[$d]['calcium'];

//Logger::log("[getMyStats] D: $d CALORIES: ".$acts[$d]['calories']."  CALS: $cals");
                $daycount++;
                if ($daycount >= 7) break;
            }
        }
//Logger::log("[getMyStats] CALS: $cals DAYCOUNT: $daycount");
        if ($daycount > 0) {
            $stats['prev']['cals']          = round($cals/$daycount);
            $stats['prev']['calories_from_fat'] = round($calories_from_fat/$daycount);
            $stats['prev']['saturated_fat'] = round($saturated_fat/$daycount);
            $stats['prev']['trans_fat']     = round($trans_fat/$daycount);
            $stats['prev']['protein']       = round($protein/$daycount);
            $stats['prev']['total_carbs']   = round($total_carbs/$daycount);
            $stats['prev']['sugars']        = round($sugars/$daycount);
            $stats['prev']['fiber']         = round($fiber/$daycount);
            $stats['prev']['total_fat']     = round($total_fat/$daycount);
            $stats['prev']['cholesterol']   = round($cholesterol/$daycount);
            $stats['prev']['sodium']        = round($sodium/$daycount);
            $stats['prev']['vitamin_a']     = round($vitamin_a/$daycount);
            $stats['prev']['vitamin_c']     = round($vitamin_c/$daycount);
            $stats['prev']['iron']          = round($iron/$daycount);
            $stats['prev']['calcium']       = round($calcium/$daycount);

            $stats['prev']['total_fat_dvp']      = round((($total_fat/$daycount) / Setup::$DV['total_fat'])*100);
            $stats['prev']['total_carbs_dvp']    = round((($total_carbs/$daycount) / Setup::$DV['total_carbs'])*100);
            $stats['prev']['saturated_fat_dvp']  = round((($saturated_fat/$daycount) / Setup::$DV['saturated_fat'])*100);
            $stats['prev']['fiber_dvp']          = round((($fiber/$daycount) / Setup::$DV['fiber'])*100);
            $stats['prev']['cholesterol_dvp']    = round((($cholesterol/$daycount) / Setup::$DV['cholesterol'])*100);
            $stats['prev']['sodium_dvp']         = round((($sodium/$daycount) / Setup::$DV['sodium'])*100);
            $stats['prev']['protein_dvp']        = round((($protein/$daycount) / Setup::$DV['protein'])*100);




        } else {
            $stats['prev']['cals'] = 0;
        }

        return $stats;
    }

    public function calculateAverageNutritionValue(){

    }

    public function updateWith($post) {
       if (!isset($post['uid']) || !isset($post['tid']) || !isset($post['coeff']) || !isset($post['prop']) || !isset($post['size']) ) {
            $response['status'] = 'error';
            $response['message']= 'request failure';
        } else {
            $db = $this->db;
            $uid = $db->escapeString(trim($post['uid']));
            $tid = $db->escapeString(trim($post['tid']));
            $coeff = $db->escapeString(trim($post['coeff']));
            $prop = $db->escapeString(trim($post['prop']));
            $size = $db->escapeString(trim($post['size']));

            $usercode = '';
            list($usercode,$timezone) = $this->readCookie();
            if (!$usercode) {
                $response['status'] = 'error';
                $response['message']= 'authentication failure';
            } else {

                $wf = $this->getSingleValue("SELECT withfood as value FROM foodlog WHERE tid=$tid AND uid=$uid AND usercode='$usercode'");
                $with = $this->extractWith($wf);

                if (isset($with[$prop]) && ($coeff < 0) ) {
                    unset($with[$prop]);
                }

                if (!isset($with[$prop]) && ($coeff > 0) ) {
                    $with[$prop] = $size;
                }

                $newf = $this->packWith($with);

                $this->q("UPDATE foodlog
                          SET withfood='$newf'
                          WHERE usercode='$usercode' AND tid=$tid AND uid=$uid");

                $response['status'] = 'ok';
                $response['message']= $uid;
            }
        }
        $nutrition = $this->calculateDailyNutritionValue($usercode, 0);
        $response['nutrition'] = $nutrition;
        return $response;
    }

    public function packWith($w) {
        $f = '';
        foreach ($w as $prop => $size) {
            $f.= "$prop:$size,";
        }
        return trim($f,',');
    }

    public function extractWith($w) {
        $with = array();
        if ($w) {
            $added = explode(',',$w);
            if (is_array($added) && (count($added)>0))
                foreach ($added as $a) {
                    $p = explode(':',$a);
                    $with[$p[0]] = $p[1];
                }
        }
        return $with;
    }

    public function deleteAction($post) {
       if (!isset($post['uid']) || !isset($post['tid'])) {
            $response['status'] = 'error';
            $response['message']= 'request failure';
        } else {
            $db = $this->db;
            $uid = $db->escapeString(trim($post['uid']));
            $tid = $db->escapeString(trim($post['tid']));

            $usercode = '';
            list($usercode,$timezone) = $this->readCookie();
            if (!$usercode) {
                $response['status'] = 'error';
                $response['message']= 'authentication failure';
            } else {
                $ts = time();
                $this->q("UPDATE foodlog
                          SET deleted_y='Y'
                          WHERE usercode='$usercode' AND tid=$tid AND uid=$uid");

                $response['status'] = 'ok';
                $response['message']= $uid;
            }
        }
        $nutrition = $this->calculateDailyNutritionValue($usercode, 0);
        $response['nutrition'] = $nutrition;
        return $response;
    }

    public function updateSize($post) {

        // validate post
        //
        if (!isset($post['uid']) || !isset($post['tid']) || !isset($post['size']) ) {
            $response['status'] = 'error';
            $response['message']= 'request failure';
        } else {
            $db = $this->db;
            $uid = $db->escapeString(trim($post['uid']));
            $tid = $db->escapeString(trim($post['tid']));
            $size = $db->escapeString(trim($post['size']));
            
            $usercode = '';
            list($usercode,$timezone) = $this->readCookie();
            if (!$usercode) {
                $response['status'] = 'error';
                $response['message']= 'authentication failure';
            } else {
                $ts = time();
                $this->q("UPDATE foodlog
                          SET size_us=$size
                          WHERE usercode='$usercode' AND tid=$tid AND uid=$uid");
                
                $response['status'] = 'ok';
                $response['message']= $uid;
            }
        }
        $nutrition = $this->calculateDailyNutritionValue($usercode, 0);
        $response['nutrition'] = $nutrition;
        return $response;
    }

    // date is in format 'mm/dd/yyyy' - tbd, ts?
    public function calculateDailyNutritionValue($usercode,$date,$food=array(),$more=array(),$actions=array()) {

        if (is_array($date)) {
            $day = $date['day'];
            $month = $date['month'];
            $year = $date['year'];
        } else if (is_numeric($date) && ($date > 0) ) {
            $day = date("j",$ts);
            $month = date("n",$ts);
            $year = date("Y",$ts);
        } else {
            $ts = strtotime($date);
            if (!$ts) $ts = time();
            $day = date("j",$ts);
            $month = date("n",$ts);
            $year = date("Y",$ts);
        }



        // load table more @TODO => remove $more
        //
        if (count($more)<1) $more = $this->getSqlArray("SELECT * FROM more");

        // load table food
        //
        if (count($food)<1) $food = $this->getSqlArray("SELECT * FROM food",true,'name');

        // load table actions @TODO fix a DB, add foodid & actionid into foodlog
        //
        if (count($actions)<1) $actions = $this->getSqlArray("SELECT * FROM actions",true,'tid');
        
        // get food log for this day, append necessary food
        //
        /*
        $sql = "SELECT  l.foodid, l.tid, l.size_us, l.withfood,
                        f.name, f.calories, f.cholesterol, f.sodium, f.total_carbs,
                        f.sugar, f.protein, f.fiber, f.vitamin_a, f.vitamin_c,
                        f.iron, f.calcium, f.total_fat, f.defsize
                FROM    foodlog l
                LEFT JOIN food f ON (l.tid=f.id)
                WHERE   (l.usercode = '$usercode')
                        AND (l.year=$year)
                        AND (l.month=$month)
                        AND (l.day=$day)";
        */
        $sql = "SELECT  *
                FROM    foodlog l
                WHERE   (l.usercode = '$usercode')
                        AND (l.year=$year)
                        AND (l.month=$month)
                        AND (l.day=$day)
                        AND (l.deleted_y IS NULL)";
        $flog = $this->getSqlArray($sql);

        $currentcalories = 0;
        $protein = 0;
        $total_carbs = 0;
        $sugars = 0;
        $fiber = 0;
        $total_fat = 0;
        $cholesterol = 0;
        $sodium = 0;
        $vitamin_a = 0;
        $vitamin_c = 0;
        $iron = 0;
        $calcium = 0;

        foreach ($flog as $f) {

            $tid = $f['tid'];
            $ussize = $actions[$tid]['defs_us'];
            $nval = $actions[$tid]['nval'];
            $calories = $food[$nval]['calories'];
            $currentussize = isset($f['size_us'])?$f['size_us']:$ussize;
            $currentsize = ((isset($f['size_us'])&&($f['size_us']>0))?$f['size_us']:$ussize);       // @TODO: change for updated items!111
            $sratio = $currentsize/$ussize;
            
            $currentcalories+= floor($calories*$sratio);
            $protein    += ((float)$food[$nval]['protein'])*$sratio;
            $total_carbs+= ((float)$food[$nval]['total_carbs'])*$sratio;
            $sugars     += ((float)$food[$nval]['sugar'])*$sratio;
            $fiber      += ((float)$food[$nval]['fiber'])*$sratio;
            $total_fat  += ((float)$food[$nval]['total_fat'])*$sratio;
            $cholesterol+= ((float)$food[$nval]['cholesterol'])*$sratio;
            $sodium     += ((float)$food[$nval]['sodium'])*$sratio;
            $vitamin_a  += ((float)$food[$nval]['vitamin_a'])*$sratio;
            $vitamin_c  += ((float)$food[$nval]['vitamin_c'])*$sratio;
            $iron       += ((float)$food[$nval]['iron'])*$sratio;
            $calcium    += ((float)$food[$nval]['calcium'])*$sratio;

            $j = 1;
            $with = array();
            if (isset($f['withfood'])) {
                $with = $this->extractWith($f['withfood']);
            }

            if (count($with)>0)
            foreach ($with as $m => $s) {
                $ratio = $with[$m] / $food[$m]['defsize'];
                $adding = $ratio*$food[$m]['calories'];
//Logger::log("$day MORE before $nval sodium: $sodium (ratio=$ratio, sratio=$sratio)");
                $protein    += ((float)$food[$m]['protein'])*$ratio*$sratio;
                $total_carbs+= ((float)$food[$m]['total_carbs'])*$ratio*$sratio;
                $sugars     += ((float)$food[$m]['sugar'])*$ratio*$sratio;
                $fiber      += ((float)$food[$m]['fiber'])*$ratio*$sratio;
                $total_fat  += ((float)$food[$m]['total_fat'])*$ratio*$sratio;
                $cholesterol+= ((float)$food[$m]['cholesterol'])*$ratio*$sratio;
                $sodium     += ((float)$food[$m]['sodium'])*$ratio*$sratio;
                $vitamin_a  += ((float)$food[$m]['vitamin_a'])*$ratio*$sratio;
                $vitamin_c  += ((float)$food[$m]['vitamin_c'])*$ratio*$sratio;
                $iron       += ((float)$food[$m]['iron'])*$ratio*$sratio;
                $calcium    += ((float)$food[$m]['calcium'])*$ratio*$sratio;
                $currentcalories+= $adding;
            }
        }

        return array(
            'calories' => round($currentcalories),
            'protein' => round($protein),
            'total_carbs' => round($total_carbs),
            'sugars' => round($sugars),
            'fiber' => round($fiber),
            'total_fat' => round($total_fat),
            'cholesterol' => round($cholesterol),
            'sodium' => round($sodium),
            'vitamin_a' => round($vitamin_a),
            'vitamin_c' => round($vitamin_c),
            'iron' => round($iron),
            'calcium' => round($calcium),
        );


    }

    public function getMyActs($tpl,$actions,$food,$sizes,$usercode) {

        $myacts = array();

        if (isset($usercode) ) {

            $ts = time();
            $today = date("M d, Y",$ts);

            // past 30 days
            //
            $back = time() - 2592000;
            $sql = "SELECT *
                    FROM foodlog
                    WHERE usercode='$usercode' AND ts > $back AND deleted_y IS NULL
                    ORDER BY ts DESC";
            $flog = $this->getSqlArray($sql);

            $pday = ''; // previous day
            $protein = 0;
            $total_carbs = 0;
            $sugars = 0;
            $fiber = 0;
            $total_fat = 0;
            $cholesterol = 0;
            $sodium = 0;
            $vitamin_a = 0;
            $vitamin_c = 0;
            $iron = 0;
            $calcium = 0;
            $calories_from_fat = 0;
            $saturated_fat = 0;
            $trans_fat = 0;

            foreach ($flog as $f) {

                $day = date("M d, Y",$f['ts']);

                if ($day != $pday) {
                    $protein = 0;
                    $total_carbs = 0;
                    $sugars = 0;
                    $fiber = 0;
                    $total_fat = 0;
                    $cholesterol = 0;
                    $sodium = 0;
                    $vitamin_a = 0;
                    $vitamin_c = 0;
                    $iron = 0;
                    $calcium = 0;
                    $calories_from_fat = 0;
                    $saturated_fat = 0;
                    $trans_fat = 0;
                    $pday = $day;
                }
                
                $older = ($day != $today);
                $disabled = ($older?'disabled':'');
                $hidden = ($older?'displaynone':'');
                
                $foodid = $f['foodid'];
                $uid = $f['uid'];
                $tid = $f['tid'];

                $icon = $actions[$tid]['icon'];
                $name = $actions[$tid]['name'];
                $ussize = $actions[$tid]['defs_us'];
                $nval = $actions[$tid]['nval'];
                $size = $actions[$tid]['size'];
                $calories = $food[$nval]['calories'];
                $datetime = date("Y/m/j H:i:s",$f['ts']);
                $currentussize = isset($f['size_us'])?$f['size_us']:$ussize;

                $currentsize = ((isset($f['size_us'])&&($f['size_us']>0))?$f['size_us']:$ussize);       // @TODO: change for updated items!111
                $sratio = $currentsize/$ussize;
                $currentcalories = $calories*$sratio;
//Logger::log("$day BEFORE $nval sodium: $sodium (sratio=$sratio)");
                $protein    += ((float)$food[$nval]['protein'])*$sratio;
                $total_carbs+= ((float)$food[$nval]['total_carbs'])*$sratio;
                $sugars     += ((float)$food[$nval]['sugar'])*$sratio;
                $fiber      += ((float)$food[$nval]['fiber'])*$sratio;
                $total_fat  += ((float)$food[$nval]['total_fat'])*$sratio;
                $cholesterol+= ((float)$food[$nval]['cholesterol'])*$sratio;
                $sodium     += ((float)$food[$nval]['sodium'])*$sratio;
                $vitamin_a  += ((float)$food[$nval]['vitamin_a'])*$sratio;
                $vitamin_c  += ((float)$food[$nval]['vitamin_c'])*$sratio;
                $iron       += ((float)$food[$nval]['iron'])*$sratio;
                $calcium    += ((float)$food[$nval]['calcium'])*$sratio;
                $calories_from_fat += ((float)$food[$nval]['calories_from_fat'])*$sratio;
                $saturated_fat += ((float)$food[$nval]['saturated_fat'])*$sratio;
                $trans_fat += ((float)$food[$nval]['trans_fat'])*$sratio;
                
//Logger::log("$day AFTER $nval sodium: $sodium (sratio=$sratio)");
                $sizeoptions = '';
                $i = 0;
                foreach ($sizes[$size]['US']['size'] as $s) {
                    if ($currentsize == $s) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    $sizeoptions.=
                        '<option val="'.$i.'" '.$selected.'>'.$sizes[$size]['US']['size'][$i].$sizes[$size]['US']['units'].' ('.
                        $sizes[$size]['US']['fsize'][$i].
                        $sizes[$size]['US']['funits'].
                        ')</option>';
                    $i++;
                }
                $more = '';
                $j = 1;
                $with = array();

                if (isset($f['withfood'])) {
                    $with = $this->extractWith($f['withfood']);
                }
                
                if (isset($actions[$tid]['more']) && is_array($actions[$tid]['more']) && ( count($actions[$tid]['more']) > 0) )
                foreach (array_keys($actions[$tid]['more']) as $m) {

                    if (isset($with[$m])) {
                        $ratio = $with[$m] / $food[$m]['defsize'];
                        $adding = $ratio*$food[$m]['calories'];
//Logger::log("$day MORE before $nval sodium: $sodium (ratio=$ratio, sratio=$sratio)");
                        $protein    += ((float)$food[$m]['protein'])*$ratio*$sratio;
                        $total_carbs+= ((float)$food[$m]['total_carbs'])*$ratio*$sratio;
                        $sugars     += ((float)$food[$m]['sugar'])*$ratio*$sratio;
                        $fiber      += ((float)$food[$m]['fiber'])*$ratio*$sratio;
                        $total_fat  += ((float)$food[$m]['total_fat'])*$ratio*$sratio;
                        $cholesterol+= ((float)$food[$m]['cholesterol'])*$ratio*$sratio;
                        $sodium     += ((float)$food[$m]['sodium'])*$ratio*$sratio;
                        $vitamin_a  += ((float)$food[$m]['vitamin_a'])*$ratio*$sratio;
                        $vitamin_c  += ((float)$food[$m]['vitamin_c'])*$ratio*$sratio;
                        $iron       += ((float)$food[$m]['iron'])*$ratio*$sratio;
                        $calcium    += ((float)$food[$m]['calcium'])*$ratio*$sratio;
                        $calories_from_fat += ((float)$food[$nval]['calories_from_fat'])*$sratio*$ratio;
                        $saturated_fat += ((float)$food[$nval]['saturated_fat'])*$sratio*$ratio;
                        $trans_fat += ((float)$food[$nval]['trans_fat'])*$sratio*$ratio;

                        $currentcalories+= $adding;
//Logger::log("$day MORE after $nval sodium: $sodium (ratio=$ratio, sratio=$sratio)");
                        $checked = 'checked';
                    } else {
                        $checked = '';
                    }

                    $more.= "<nobr><input type=\"checkbox\" onclick=\"updateWith($j,$tid,$uid)\" id=\"with$uid".'-'."$j\" prop=\"$m\" defsize=\"".$actions[$tid]['more'][$m]."\"  $disabled $checked><label for=\"with$uid".'-'."$j\">$m</label></nobr> &nbsp;";
                    $j++;
                }
                $item = $tpl;
                $item = preg_replace('/__FOODID__/',$foodid,$item);
                $item = preg_replace('/__UID__/',$uid,$item);
                $item = preg_replace('/__ICON__/',$icon,$item);
                $item = preg_replace('/__NAME__/',$name,$item);
                $item = preg_replace('/__TID__/',$tid,$item);
                $item = preg_replace('/__SIZE_OPTIONS__/',$sizeoptions,$item);
                $item = preg_replace('/__CALORIES__/',$calories,$item);
                $item = preg_replace('/__CURRENT__/',floor($currentcalories),$item);
                $item = preg_replace('/__DATETIME__/',$datetime,$item);
                $item = preg_replace('/__CURRENT_SIZE__/',$currentsize,$item);
                $item = preg_replace('/__CURRENT_US_SIZE__/',$currentussize,$item);
                $item = preg_replace('/__US_SIZE__/',$ussize,$item);
                $item = preg_replace('/__MORE__/',$more,$item);
                $item = preg_replace('/__DISABLED__/',$disabled,$item);
                $item = preg_replace('/__HIDDEN__/',$hidden,$item);

                $myacts[$day]['items'][] = $item;

                if (isset($myacts[$day]['calories'])) {
                    $myacts[$day]['calories']+= $currentcalories;
                } else {
                    $myacts[$day]['calories'] = $currentcalories;
                }

//                if ($pday != $day) {

                    $myacts[$day]['protein']    = $protein;
                    $myacts[$day]['total_carbs']= $total_carbs;
                    $myacts[$day]['sugars']     = $sugars;
                    $myacts[$day]['fiber']      = $fiber;
                    $myacts[$day]['total_fat']  = $total_fat;
                    $myacts[$day]['cholesterol']= $cholesterol;
                    $myacts[$day]['sodium']     = $sodium;
                    $myacts[$day]['vitamin_a']  = $vitamin_a;
                    $myacts[$day]['vitamin_c']  = $vitamin_c;
                    $myacts[$day]['iron']       = $iron;
                    $myacts[$day]['calcium']    = $calcium;
                    $myacts[$day]['calories_from_fat'] = $calories_from_fat;
                    $myacts[$day]['saturated_fat'] = $saturated_fat;
                    $myacts[$day]['trans_fat'] = $trans_fat;

                    // daily values 2,000 calorie diet

                    $myacts[$day]['total_fat_dvp']      = round(($total_fat / Setup::$DV['total_fat'])*100);
                    $myacts[$day]['total_carbs_dvp']    = round(($total_carbs / Setup::$DV['total_carbs'])*100);
                    $myacts[$day]['saturated_fat_dvp']  = round(($saturated_fat / Setup::$DV['saturated_fat'])*100);
                    $myacts[$day]['fiber_dvp']          = round(($fiber / Setup::$DV['fiber'])*100);
                    $myacts[$day]['cholesterol_dvp']    = round(($cholesterol / Setup::$DV['cholesterol'])*100);
                    $myacts[$day]['sodium_dvp']         = round(($sodium / Setup::$DV['sodium'])*100);
                    $myacts[$day]['protein_dvp']        = round(($protein / Setup::$DV['protein'])*100);
                    
//Logger::log("$day ASSIGNING $nval sodium: $sodium ");

//                    $pday = $day;
                    
//                } else {
//
//                    $myacts[$day]['protein']    += $protein;
//                    $myacts[$day]['total_carbs']+= $total_carbs;
//                    $myacts[$day]['sugars']     += $sugars;
//                    $myacts[$day]['fiber']      += $fiber;
//                    $myacts[$day]['total_fat']  += $total_fat;
//                    $myacts[$day]['cholesterol']+= $cholesterol;
//                    $myacts[$day]['sodium']     += $sodium;
//                    $myacts[$day]['vitamin_a']  += $vitamin_a;
//                    $myacts[$day]['vitamin_c']  += $vitamin_c;
//                    $myacts[$day]['iron']       += $iron;
//                    $myacts[$day]['calcium']    += $calcium;
//
//Logger::log("$day ADDING $nval sodium: $sodium SO, total=".$myacts[$day]['sodium']);
//
//                }

            }
	}
// Logger::log("ALLACTS:".  var_export($myacts, true));
        return $myacts;
    }


    ////////////////////// one time use functions  /////////////////////////

    private function insertFood() {

        foreach (array_keys(Setup::$FOOD) as $f) {
            $otherfields = implode(',',array_keys(Setup::$FOOD[$f]));
            $ov = array();
            foreach (array_keys(Setup::$FOOD[$f]) as $k) {
                array_push($ov,(isset(Setup::$FOOD[$f][$k])?Setup::$FOOD[$f][$k]:0));
            }
            $othervalues = implode(',',$ov);
            $this->q("INSERT INTO food (name,$otherfields) VALUES ('$f',$othervalues)");
        }

    }

    private function insertSizes() {

        /*
        'pie' => array(
                'US' => array(
                    'units' => 'slice',
                    'size' => array(1,2,3),
                    'alias' => array('1 small slice','1 medium slice','1 large slice'), // @TODO everywhere
                    'fsize' => array(63,126,190),
                    'funits' => 'g',
                ),
                'Metric' => array(
                    'units' => 'L',
                    'size' => array(0.3, 0.5, 0.7, 1.0, 1.5),
                ),
            ),
         */

        foreach (array_keys(Setup::$SIZES) as $s) {
            $this->q("INSERT INTO sizes (name,us_units,us_sizes,us_aliases,us_fsizes,us_funits,m_units,m_sizes)
                        VALUES (
                        '$s',
                        '".Setup::$SIZES[$s]['US']['units']."',
                        '".implode(',',Setup::$SIZES[$s]['US']['size'])."',
                        '".implode(',',Setup::$SIZES[$s]['US']['alias'])."',
                        '".implode(',',Setup::$SIZES[$s]['US']['fsize'])."',
                        '".Setup::$SIZES[$s]['US']['funits']."',
                        '".Setup::$SIZES[$s]['Metric']['units']."',
                        '".implode(',',Setup::$SIZES[$s]['Metric']['size'])."'
                        )
                    ");
        }

    }


    public function insertActions() {

        foreach (array_keys(Setup::$ACTIONS) as $a) {

            $name       = Setup::$ACTIONS[$a]['name'];
            $pic        = Setup::$ACTIONS[$a]['pic'];
            $icon       = Setup::$ACTIONS[$a]['icon'];
            $size       = Setup::$ACTIONS[$a]['size'];
            $def_us     = Setup::$ACTIONS[$a]['defs']['US'];
            $def_metric = Setup::$ACTIONS[$a]['defs']['Metric'];
            $tid        = Setup::$ACTIONS[$a]['id'];
            $nval       = Setup::$ACTIONS[$a]['nval'];

            $food_id    = $this->getSingleValue("SELECT id as value FROM food WHERE name='$nval'");
            $size_id    = $this->getSingleValue("SELECT id as value FROM sizes WHERE name='$size'");

            $this->q("INSERT INTO actions
                            (name,pic,icon,defs_us,defs_metric,size_id,food_id,tid,size,nval)
                        VALUES
                            ('$name','$pic','$icon',$def_us,$def_metric,$size_id,$food_id,$tid,'$size','$nval')
                        ");
            $id = $this->getGeneratedId();
            $more = Setup::$ACTIONS[$a]['more'];
            
            if (is_array($more) && (count($more)>0))
            foreach($more as $m => $weight) {

                // get additional food id
                $aid = $this->getSingleValue("SELECT id as value FROM food WHERE name='$m'");

                // insert into more table
                $this->q("INSERT INTO more (action_id,add_food_id,add_size_g) VALUES ($id,$aid,$weight)");
            }
            
        }


    }

    ///////////////////////////////////////////////////////

    public function getActions($groups) {

        /*
            2=>array(
		'id'	=> 2,
		'name'	=> 'Soup',
		'pic'	=> 'f_32.png',
		'icon'	=> '50/2.soup.png',
                'size'  => 'cup',
                'defs'  => array('US'=>8,'Metric'=>200),
                'nval'  => 'soup',
                'more'  => array (
                    'tomatoes' => 85, //g
                    'chicken'=> 56, //
                    'veggies'=> 56, //
                    'pasta'  => 28, //
                    'beef' => 56, //
                    'eggs'=> 45, //
                    'fish'=> 28, //
                    'potatoes'=> 28, //
                    'carrot'=> 10, //
                    ),

		),
         */
        $acts = $this->getSqlArray("SELECT * FROM actions ORDER BY tid ASC",true,'id');

//        $sql = "SELECT  a.id, a.name, a.pic, a.icon, a.defs_us, a.defs_metric,
//                        a.size_id, a.food_id, a.tid, a.size, a.nval, g.group
//                FROM    actions a
//                LEFT JOIN groups g ON (a.food_id = g.food_id)
//                ORDER BY tid ASC";
//
//        $acts = $this->getSqlArray($sql,true,'id');
        
        $more = $this->getSqlArray(
                "SELECT
                m.action_id as id,m.add_food_id as aid,f.name as name,m.add_size_g as size
                FROM more m LEFT JOIN food f ON (f.id=m.add_food_id)"
                );

        // attach more to actions
        foreach ($more as $m) {
            $acts[$m['id']]['more'][$m['name']] = (int)$m['size'];
        }

        $actions = array();
        $ga = array();

        foreach ($acts as $a) {
            $actions[$a['tid']] = $a;
            $actions[$a['tid']]['foodid'] = $a['id'];
            $actions[$a['tid']]['id'] = $a['tid'];
            $actions[$a['tid']]['defs'] = array();
            $actions[$a['tid']]['defs']['US'] = $a['defs_us'];
            $actions[$a['tid']]['defs']['Metric'] = $a['defs_metric'];

            foreach ($groups as $g) {
                if ($g['food_id'] == $actions[$a['tid']]['food_id']) {
                    $ga[$g['group']][] = $actions[$a['tid']];
                }
            }

        }


        // return $actions;

        return array($ga,$actions);




    }

    public function getSizes() {
        /*
        'pie' => array(
                'US' => array(
                    'units' => 'slice',
                    'size' => array(1,2,3),
                    'alias' => array('1 small slice','1 medium slice','1 large slice'), // @TODO everywhere
                    'fsize' => array(63,126,190),
                    'funits' => 'g',
                ),
                'Metric' => array(
                    'units' => 'L',
                    'size' => array(0.3, 0.5, 0.7, 1.0, 1.5),
                ),
            ),
         */
        $sz = $this->getSqlArray("SELECT * FROM sizes ORDER BY id ASC");
        $sizes = array();
        foreach ($sz as $s) {
            $sizes[$s['name']]['US']['units'] = $s['us_units'];
            $sizes[$s['name']]['US']['size'] = explode(',',$s['us_sizes']);
            $sizes[$s['name']]['US']['alias'] = explode(',',$s['us_aliases']);
            $sizes[$s['name']]['US']['fsize'] = explode(',',$s['us_fsizes']);
            $sizes[$s['name']]['US']['funits'] = $s['us_funits'];
        }

        return $sizes;

    }

    public function getFood() {

        /*
        'tea'  => array (          // nutritional value per default size, in grams
                'calories' => 3,        // tea=2, coffee=5
                'calories_from_fat' => 0,
                'total_fat' => 0,
                'saturated_fat' => 0,
                'polyunsaturated_fat' => 0,
                'monounsaturated_fat' => 0,
                'cholesterol' => 0,
                'sodium' => 7,          // mg
                'total_carbs' => 0.7,     // g
                'sugar' => 0,
                'fiber' => 0,
                'protein' => 0,
                'vitamin_a' => NULL,       // all vitamins in % of daily value
                'vitamin_c' => NULL,
                'vitamin_d' => NULL,
                'vitamin_e' => NULL,
                'vitamin_k' => NULL,
                'calcium' => NULL,
                'iron' => NULL,
                'defisize' => 227,
            ),
         */

        $food = $this->getSqlArray("SELECT * FROM food ORDER BY id ASC",true,'name');
        return $food;
    }

    public function updateFoodLogDates() {

        // TS -> year, month, day, weekday, week
        $flog = $this->getSqlArray("SELECT * FROM foodlog");
        foreach ($flog as $f) {

            $year = (int)date("Y",$f['ts']);
            $month = (int)date("n",$f['ts']);
            $day = (int)date("j",$f['ts']);
            $weekday = (int)date("N",$f['ts']); // Monday = 1, Sunday = 7
            $week = (int)date("W",$f['ts']);
            $id = $f['id'];

            $this->q("UPDATE foodlog set year=$year, month=$month, day=$day, weekday=$weekday, week=$week WHERE id=$id");
        }

    }

    public function readCookie() {
        $usercode = '';
        $tdelta = '';
        if (isset($_COOKIE[Setup::$USER_COOKIE]) ) {
            $cookie = $_COOKIE[Setup::$USER_COOKIE];
            if (!strrpos($cookie, '|')) {
                $usercode = $cookie;
                $tdelta = Setup::$DEFAULT_TIMEZONE;
                setcookie(Setup::$USER_COOKIE, "$usercode|$tdelta", time() + 10000000000, '/', Setup::$BASE_DOMAIN);
            } else {
                list($usercode,$tdelta) = explode('|',$_COOKIE[Setup::$USER_COOKIE]);
            }
        }
        return array($usercode,$tdelta);
    }

    public function changeTimeZone($post) {
        
        // @TODO
        // validate post
        //
        $response = array();
        if (!isset($post['tind'])) {
            $response['status'] = 'error';
            $response['message']= 'request failure';
        } else {
            $db = $this->db;
            $tind = $db->escapeString(trim($post['tind']));
            if (!$this->isNum($tind) || ($tind <= 0)) {
                $response['status'] = 'error';
                $response['message']= 'corrupt input';
            } else {
                list($usercode,$tdelta) = $this->readCookie();
//if (Setup::$DEBUG) Logger::log("[changeTimeZone] usercode: $usercode, tdelta: $tdelta ");

                $newtz = $this->getSqlArray("SELECT * FROM timezones WHERE id=$tind");
//if (Setup::$DEBUG) Logger::log("[changeTimeZone] newtz: ".  var_export($newtz, true));
                $newdelta = $newtz[0]['utc_delta'];
                $newlabel = $newtz[0]['label'];
//if (Setup::$DEBUG) Logger::log("[changeTimeZone] newdelta: $newdelta ");
                setcookie(Setup::$USER_COOKIE, "$usercode|$newdelta", time() + 36000000, "/", Setup::$BASE_DOMAIN);

                $this->q("UPDATE users SET utc_delta=$newdelta WHERE usercode='$usercode'");

                $response['status'] = 'ok';
                $response['message']= $newlabel;
            }
        }

        return $response;
        
    }

    public function getMyTimeZone($tdelta=null) {
        if (!isset($tdelta) && isset($_COOKIE[Setup::$USER_COOKIE]) ) {
            list($usercode,$tdelta) = explode('|',$_COOKIE[Setup::$USER_COOKIE]);
	}
        if (!isset($tdelta)) $tdelta = -(Setup::$DEFAULT_TIMEZONE);
        $mytz = $this->getSingleValue("SELECT label as value FROM timezones WHERE utc_delta=$tdelta");
//if (Setup::$DEBUG) Logger::log("[getMyTimezone] sql: SELECT label as value FROM timezones WHERE utc_delta=$tdelta ");
        if (!$mytz) $mytz = "Not defined";
        return "Your timezone: ".$mytz;
    }

    public function getChangeTZ($mytz=0) {

        if (!$mytz) $mytz = Setup::$SERVER_TIMEZONE_DELTA;

        $zones = $this->getSqlArray("SELECT * FROM timezones ORDER BY ID ASC");
        $tz = "<select name='timezones' id='changetimezone'>";
        foreach ($zones as $z) {
            $id = $z['id'];
            $label = $z['label'];
            if ($z['utc_delta'] == $mytz) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $tz.= "<option val=\"$id\" $selected>$label</option>";
        }
        $tz.= "</select>";
        return $tz;
    }

    public function addAction($post) {

        $response = array();

        // setting up data tables (transfer from Setup:: constants)
        // DON'T CHANGE TO TRUE! unless you are rebuilding DB
        if (false) {
            //$this->importUSDADatabase();
            //$this->insertFood();
            //$this->insertSizes();
            //$this->insertActions();
            return $response;
        }

        if (false) {
            $this->updateFoodLogDates();
        }

        // validate post
        //
        if (!isset($post['uid']) || !isset($post['tid']) || !isset($post['foodid']) ) {
            $response['status'] = 'error';
            $response['message']= 'request failure';
        } else {
            $db = $this->db;
            $uid = $db->escapeString(trim($post['uid']));
            $tid = $db->escapeString(trim($post['tid']));
            $foodid = $db->escapeString(trim($post['foodid']));

            if (!$this->isNum($uid) || !$this->isNum($tid) || !$this->isNum($foodid)) {
                $response['status'] = 'error';
                $response['message']= 'corrupt input';
            } else {
                $usercode = '';
                list($usercode,$tsdelta) = $this->readCookie();
                if (!$tsdelta) $tsdelta = Setup::$DEFAULT_TIMEZONE;
                if (!$usercode) {
                    $response['status'] = 'error';
                    $response['message']= 'authentication failure';
                } else {

                    $srvtime = time();
//Logger::log("SRVTIME: $srvtime, TSDELTA: $tsdelta, +".(3600*date('I')));
                    $ts = $srvtime + $tsdelta + Setup::$SERVER_TIMEZONE_DELTA;
                    //+ 3600*date('I'); // 9:10 pm PT => 21:10 pm PT + +3 +8 ( 9 + 3 + 8) + summer time
                    $year = (int)date("Y",$ts);
                    $month = (int)date("n",$ts);
                    $day = (int)date("j",$ts);
                    $weekday = (int)date("N",$ts); // Monday = 1, Sunday = 7
                    $week = (int)date("W",$ts);

                    $this->q("INSERT INTO foodlog 
                                (usercode,foodid,ts,tid,uid,year,month,day,weekday,week)
                              VALUES
                                ('$usercode',$foodid,$ts,$tid,$uid,$year,$month,$day,$weekday,$week)");
                    $response['status'] = 'ok';
                    $response['message']= $uid;
                }
            }
        }

        $date['day'] = $day;
        $date['month'] = $month;
        $date['year'] = $year;
        $nutrition = $this->calculateDailyNutritionValue($usercode, $date);
        $response['nutrition'] = $nutrition;

        return $response;

    }

    private function importUSDADatabase() {

        $dump = 'USDADump.sql';
        $USDADir = 'C:\\www\\ration.me\\USDA\\';
        $USDAMap = array(
            'usda_data_src'     => 'data_src.txt',
            'usda_datsrcln'     => 'datsrcln.txt',
            'usda_deriv_cd'     => 'deriv_cd.txt',
            'usda_fd_group'     => 'fd_group.txt',
            'usda_food_des'     => 'food_des.txt',
            'usda_footnote'     => 'footnote.txt',
            'usda_langdesc'     => 'langdesc.txt',
            'usda_langual'      => 'langual.txt',
            'usda_nut_data'     => 'nut_data.txt',
            'usda_nutr_def'     => 'nutr_def.txt',
            'usda_src_cd'       => 'src_cd.txt',
            'usda_weight'       => 'weight.txt',

        );
        $USDAFields = array(
            'usda_food_des'     => array(
                'ndb_no','fdgrp_cd','long_desc','shrt_desc','comname','manufacname',
                'survey','ref_desc','refuse','sciname','n_factor','pro_factor',
                'fat_factor','cho_factor',
            ),
            'usda_datsrcln'     => array(
                'ndb_no','nutr_no','datasrc_id'
            ),
            'usda_deriv_cd'     => array(
                'deriv_cd','deriv_desc',
            ),
            'usda_fd_group'     => array(
                'fdgrp_cd','fdgrp_desc',
            ),
            'usda_data_src'     => array(
                'datasrc_id','authors','title','year','journal','vol_city',
                'issue_state','start_page','end_page',
            ),
            'usda_footnote'     => array(
                'ndb_no','footnt_no','footnt_typ','nutr_no','footnt_txt',
            ),
            'usda_langdesc'     => array(
                'factor_code','description',
            ),
            'usda_langual'      => array(
                'ndb_no','factor_code',
            ),
            'usda_nut_data'     => array(
                'ndb_no','nutr_no','nutr_val','num_data_pts','std_error','src_cd',
                'deriv_cd','ref_ndb_no','add_nutr_mark','num_studies','min','max',
                'df','low_eb','up_eb','stat_cmt','cc',
            ),
            'usda_nutr_def'     => array(
                'nutr_no','units','tagname','nutrdesc','num_dec','sr_order',
            ),
            'usda_src_cd'       => array(
                'src_cd','srccd_desc',
            ),
            'usda_weight'       => array(
                'ndb_no','seq','amount','msre_desc','gm_wgt','num_data_pts','std_dev',
            ),

        );

        $numbers = array(
            'refuse','n_factor','pro_factor','fat_factor','cho_factor','nutr_val',
            'num_data_pts','std_error','num_studies','min','max','df','low_eb',
            'up_eb','sr_order','amount','gm_wgt','std_dev',
        );

        $logfile = fopen($USDADir.'sql\\dump.log','w');
        
        foreach ($USDAMap as $table => $file) {

            //if ($table != 'usda_deriv_cd') continue;

            // open dump file for writting
            //
            $dumpfile = fopen($USDADir.'sql\\'.$table.'_'.$dump,'w');
            fwrite($dumpfile,"USE `ration`; \r\n");

            // clean up the table
            fwrite($dumpfile,"DELETE FROM `$table`; \r\n");

            // read file
            //
            $filename = $USDADir.$file;
            // Logger::log("Reading $filename (table: $table)...");
            $lines = file($filename);

            // parse lines
            //
            foreach ($lines as $line_num => $line) {

                // parse the line
                $fields = explode('^',$line);
                $count_columns = count($USDAFields[$table]);
                $count_fields = count($fields);

                if ($count_columns != $count_fields) {
                    fwrite($logfile, "[$table L:$line_num c:$count_columns != f:$count_fields] $line\n");
                    continue;
                }

                // insert line into corresponding table
                $sql = "INSERT INTO `$table` (`";
                $sql.= implode('`,`',$USDAFields[$table]);
                $sql.= "`) VALUES (";
                $i = 0;
                $pv = array();
                foreach($fields as $f) {
                    // clean up from '~'
                    $f = trim(preg_replace('/~/', '', $f));
                    // see if it's a number
                    if (in_array($USDAFields[$table][$i], $numbers)) {
                        $f = ($f?$f:0);
                        $pv[] = $f;
                    } else {
                        $f = preg_replace("/'/","\'",$f);
                        $pv[] = "'$f'";
                    }
                    $i++;
                }
                $sql.= implode(',',$pv);
                $sql.= "); ";

                // write a record
                fwrite($dumpfile,"$sql\r\n");

                //Logger::log($sql);

                //$this->q($sql);
                //usleep(10000); // sleep for 10 ms

                //if (($line_num % 10) == 0) Logger::log("......$line_num");
                
            }
            // Logger::log("...parsed $line_num lines");
            fclose($dumpfile);


        }



        /*
         * ****************** USDA DB creation *********************************

CREATE  TABLE `ration`.`usda_food_des` (
  `ndb_no` CHAR(5) NOT NULL ,
  `fdgrp_cd` CHAR(4) NOT NULL ,
  `long_desc` VARCHAR(200) NOT NULL ,
  `shrt_desc` VARCHAR(60) NOT NULL ,
  `comname` VARCHAR(100) NULL ,
  `manufacname` VARCHAR(65) NULL ,
  `survey` CHAR(1) NULL ,
  `ref_desc` VARCHAR(135) NULL ,
  `refuse` INT NULL ,
  `sciname` VARCHAR(65) NULL ,
  `n_factor` DOUBLE NULL ,
  `pro_factor` DOUBLE NULL ,
  `fat_factor` DOUBLE NULL ,
  `cho_factor` DOUBLE NULL ,
  PRIMARY KEY (`ndb_no`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

CREATE  TABLE `ration`.`usda_fd_group` (
  `fdgrp_cd` CHAR(4) NOT NULL ,
  `fdgrp_desc` VARCHAR(60) NOT NULL ,
  PRIMARY KEY (`fdgrp_cd`) )
ENGINE = MyISAM;

CREATE  TABLE `ration`.`usda_langual` (
  `ndb_no` CHAR(5) NOT NULL ,
  `factor_code` CHAR(5) NOT NULL  )
ENGINE = MyISAM;

CREATE  TABLE `ration`.`usda_langdesc` (
  `factor_code` CHAR(5) NOT NULL ,
  `description` VARCHAR(140) NOT NULL ,
  PRIMARY KEY (`factor_code`) )
ENGINE = MyISAM;

CREATE  TABLE `ration`.`usda_nut_data` (
  `ndb_no` CHAR(5) NOT NULL ,
  `nutr_no` CHAR(3) NOT NULL ,
  `nutr_val` DOUBLE NOT NULL ,
  `num_data_pts` INT NOT NULL ,
  `std_error` DOUBLE NULL ,
  `src_cd` VARCHAR(2) NOT NULL ,
  `deriv_cd` VARCHAR(4) NULL ,
  `ref_ndb_no` VARCHAR(5) NULL ,
  `add_nutr_mark` CHAR(1) NULL ,
  `num_studies` INT NULL ,
  `min` DOUBLE NULL ,
  `max` DOUBLE NULL ,
  `df` INT NULL ,
  `low_eb` DOUBLE NULL ,
  `up_eb` DOUBLE NULL ,
  `stat_cmt` VARCHAR(10) NULL ,
  `cc` CHAR(1) NULL  )
ENGINE = MyISAM;

CREATE  TABLE `ration`.`usda_nutr_def` (
  `nutr_no` CHAR(3) NOT NULL ,
  `units` VARCHAR(7) NOT NULL ,
  `tagname` VARCHAR(20) NULL ,
  `nutrdesc` VARCHAR(60) NOT NULL ,
  `num_dec` CHAR(1) NOT NULL ,
  `sr_order` INT NOT NULL ,
  PRIMARY KEY (`nutr_no`) ,
  UNIQUE INDEX `nutr_no_UNIQUE` (`nutr_no` ASC) )
ENGINE = MyISAM;

CREATE  TABLE `ration`.`usda_src_cd` (
  `src_cd` VARCHAR(2) NOT NULL ,
  `srccd_desc` VARCHAR(60) NOT NULL ,
  PRIMARY KEY (`src_cd`) )
ENGINE = MyISAM;

CREATE  TABLE `ration`.`usda_deriv_cd` (
  `deriv_cd` VARCHAR(4) NOT NULL ,
  `deriv_desc` VARCHAR(120) NOT NULL ,
  PRIMARY KEY (`deriv_cd`) )
ENGINE = MyISAM;

CREATE  TABLE `ration`.`usda_weight` (
  `ndb_no` CHAR(5) NOT NULL ,
  `seq` CHAR(2) NOT NULL ,
  `amount` DOUBLE NOT NULL ,
  `msre_desc` VARCHAR(80) NOT NULL ,
  `gm_wgt` DOUBLE NOT NULL ,
  `num_data_pts` INT NULL ,
  `std_dev` DOUBLE NULL ,
  PRIMARY KEY (`ndb_no`, `seq`) )
ENGINE = MyISAM;

CREATE  TABLE `ration`.`usda_footnote` (
  `ndb_no` CHAR(5) NOT NULL ,
  `footnt_no` VARCHAR(5) NOT NULL ,
  `footnt_typ` CHAR(1) NOT NULL ,
  `nutr_no` CHAR(3) NULL ,
  `footnt_txt` VARCHAR(200) NOT NULL )
ENGINE = MyISAM;

CREATE  TABLE `ration`.`usda_datsrcln` (
  `ndb_no` CHAR(5) NOT NULL ,
  `nutr_no` CHAR(3) NOT NULL ,
  `datasrc_id` VARCHAR(6) NOT NULL ,
  PRIMARY KEY (`ndb_no`, `nutr_no`, `datasrc_id`) )
ENGINE = MyISAM;

CREATE  TABLE `ration`.`usda_data_src` (
  `datasrc_id` VARCHAR(6) NOT NULL ,
  `authors` VARCHAR(255) NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `year` VARCHAR(4) NULL ,
  `journal` VARCHAR(135) NULL ,
  `vol_city` VARCHAR(16) NULL ,
  `issue_state` VARCHAR(5) NULL ,
  `start_page` VARCHAR(5) NULL ,
  `end_page` VARCHAR(5) NULL ,
  PRIMARY KEY (`datasrc_id`) )
ENGINE = MyISAM;





         *
         */

    }

}