<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecurringTaskTemplateRequest;
use App\Http\Requests\UpdateRecurringTaskTemplateRequest;
use App\Http\Requests\UpdateTaskLabelsRequest;
use App\Models\RecurringTaskTemplate;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RecurringTaskTemplateController extends Controller
{
    public function recurring(): Response
    {
        $templates = RecurringTaskTemplate::query()
            ->with('labels', 'periods', 'weekdays')
            ->belongsToUser(auth()->id())
            ->ordered()
            ->get();

        $templates->each(function ($template) {
            $template->setRelation(
                'weekdays',
                $template->weekdays->pluck('weekday')->values()
            );
        });

        return Inertia::render('recurring/Recurring', [
            'templates' => $templates,
        ]);
    }

    public function updateLabels(UpdateTaskLabelsRequest $request, RecurringTaskTemplate $template): RedirectResponse
    {
        $template->labels()->sync($request->label_ids);

        return back()->with('success', 'Recurring template labels updated successfully.');
    }

    public function destroy(RecurringTaskTemplate $template): RedirectResponse
    {
        $template->delete();

        return back()->with('success', 'Recurring template deleted successfully.');
    }

    public function store(StoreRecurringTaskTemplateRequest $request): RedirectResponse
    {
        $template = $request->user()->recurringTaskTemplates()->make();
        $template->saveFromPayload($request->validated());
        $template->periods()->create([
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => null,
        ]);

        return back()->with('success', 'Recurring template created successfully.');
    }

    public function update(UpdateRecurringTaskTemplateRequest $request, RecurringTaskTemplate $template): RedirectResponse
    {
        $template->saveFromPayload($request->validated());

        return back()->with('success', 'Recurring template updated successfully.');
    }

    public function toggle(RecurringTaskTemplate $template): RedirectResponse
    {
        $today = Carbon::today()->toDateString();

        $openPeriod = $template->periods()
            ->whereNull('end_date')
            ->orderByDesc('start_date')
            ->first();

        if ($openPeriod) {
            if ($openPeriod->start_date->toDateString() === $today) {
                $openPeriod->delete();
            } else {
                $openPeriod->update(['end_date' => $today]);
            }

            return back()->with('success', 'Recurring template deactivated successfully.');
        }

        $template->periods()->create([
            'start_date' => $today,
            'end_date' => null,
        ]);

        return back()->with('success', 'Recurring template activated successfully.');
    }
}
