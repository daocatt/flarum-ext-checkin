<?php

namespace Gtdxyz\Checkin\Api\Controller;

use Carbon\Carbon;
use Illuminate\Database\ConnectionInterface;
use Flarum\Api\Controller\AbstractListController;
use Flarum\User\UserRepository;
use Flarum\Settings\SettingsRepositoryInterface;
use Gtdxyz\Checkin\Api\Serializer\CheckinHistorySerializer;
use Gtdxyz\Checkin\Model\UserCheckinHistory;

use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Flarum\Http\UrlGenerator;
use Illuminate\Support\Collection;

use function PHPSTORM_META\map;

class ListUserCheckinHistoryController extends AbstractListController
{
    protected $url;
    protected $db;
    protected $settings;
    public $serializer = CheckinHistorySerializer::class;

    public $include = [
        'user'
    ];

    protected $repository;

    public function __construct(SettingsRepositoryInterface $settings, UserRepository $repository, ConnectionInterface $connection, UrlGenerator $url)
    {
        $this->url = $url;
        $this->db = $connection;
        $this->repository = $repository;
        $this->settings = $settings;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        //= Special note:
        //= List to display checkin days
        //= Donnot need to check if constant is open or not.

        $constant = intval($this->settings->get('gtdxyz-checkin.constant', 0));
        $constant_days = intval($this->settings->get('gtdxyz-checkin.constant-days', 0));
        $constant_days = $constant_days <= 0 ? 1 : $constant_days;
        
        $params = $request->getQueryParams();
        $actor = $request->getAttribute('actor');

        $limit = $this->extractLimit($request)??7;
        $offset = $this->extractOffset($request)??0;
        
        // get checkindays
        $display_days = [];
        $display_days_count = 7;

        // only need this week days
        $start_week = Carbon::now()->startOfWeek()->format('Y-m-d');
        
        // if($constant && $constant == 1) {
        //     $firstDay = Carbon::now()->subDays($constant_days-1)->format('Y-m-d');
        //     $display_days_count = $constant_days;
        // } else {
        //     $firstDay = $start_week;
        // }
        
        for($i=0;$i<$display_days_count;$i++){
            $display_days[Carbon::parse($start_week)->addDays($i)->format('Y-m-d')] = 0;
        }
        
        $userID = $actor->id;
        if(!$userID){
            exit;
        }

        $checkinHistoryQuery = UserCheckinHistory::query()->where(["user_id"=>$userID]);
        $CheckinHistoryResult = $checkinHistoryQuery
            ->whereDate('checkin_time','>=', $start_week)
            ->where('constant','>',0)
            ->skip($offset)
            ->take($limit + 1)
            ->orderBy('checkin_time', 'asc')
            ->get();

        $hasMoreResults = $limit > 0 && $CheckinHistoryResult->count() > $limit;

        if($hasMoreResults){
            $CheckinHistoryResult->pop();
        }

        $historyResult = $CheckinHistoryResult->map(function ($item) use ($display_days) {
            $checkin_date = Carbon::parse($item->checkin_time)->format('Y-m-d');
            if(array_key_exists($checkin_date,$display_days)){
                $item->checkin_time = $checkin_date;
                return $item;
            }
        });
        
        $historyResult = array_merge($display_days, $historyResult->keyby('checkin_time')->toArray());

        $historyResult = new Collection($historyResult);

        $historyResult = $historyResult->map(function ($item, $key) use ($userID){
            return $item?(object)$item:(object)[
                'id'=>0,
                'user_id'=> $userID,
                'type'=>'',
                'event_id'=>0,
                'checkin_time'=>$key,
                'reward_money'=>0,
                'constant'=>'',
                'remark'=>'',
            ];
        });
        
        
        return $historyResult;

        // $document->addPaginationLinks(
        //     $this->url->to('api')->route('user.checkin.history', ['id' => $userID]),
        //     $params,
        //     $offset,
        //     $limit,
        //     $hasMoreResults ? null : 0
        // );

        // return $CheckinHistoryResult;

    }
}
