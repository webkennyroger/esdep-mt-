<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $groupsByName = DB::table('contact_groups')
            ->whereNull('owner_contact_id')
            ->get(['id', 'name'])
            ->keyBy(fn (object $group): string => mb_strtoupper(trim($group->name)));

        DB::table('contacts')
            ->whereNotNull('setor')
            ->orderBy('id')
            ->chunkById(100, function ($contacts) use ($groupsByName): void {
                foreach ($contacts as $contact) {
                    $group = $groupsByName->get(mb_strtoupper(trim($contact->setor)));

                    if (! $group || $this->hasGlobalGroup((int) $contact->id)) {
                        continue;
                    }

                    DB::table('contact_contact_group')->insertOrIgnore([
                        'contact_id' => $contact->id,
                        'contact_group_id' => $group->id,
                        'is_responsible' => false,
                        'sort_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    private function hasGlobalGroup(int $contactId): bool
    {
        return DB::table('contact_contact_group')
            ->join('contact_groups', 'contact_groups.id', '=', 'contact_contact_group.contact_group_id')
            ->where('contact_contact_group.contact_id', $contactId)
            ->whereNull('contact_groups.owner_contact_id')
            ->exists();
    }

    public function down(): void
    {
        // Vinculos reconstruidos representam dados reais e nao devem ser removidos no rollback.
    }
};
