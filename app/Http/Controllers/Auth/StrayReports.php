<?php
public function strayReports()
{
    $reports = \App\Models\StrayReport::with('adopter')->orderByDesc('reported_at')->paginate(20);
    return view('admin.stray-reports', compact('reports'));
}