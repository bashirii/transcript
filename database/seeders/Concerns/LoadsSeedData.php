<?php

namespace Database\Seeders\Concerns;

trait LoadsSeedData
{
    protected function institutionData(): array
    {
        return require database_path('seeders/data/institution.php');
    }

    protected function curriculumData(): array
    {
        return require database_path('seeders/data/bce_curriculum.php');
    }

    /**
     * Map BCE year/semester pair to semister list index (0-based).
     */
    protected function semisterIndex(int $year, int $semester): int
    {
        return (($year - 1) * 2) + ($semester - 1);
    }
}
