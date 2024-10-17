<?php

use App\Jobs\SyncUsersWithProviderInBatchJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new SyncUsersWithProviderInBatchJob())->everyMinute();
