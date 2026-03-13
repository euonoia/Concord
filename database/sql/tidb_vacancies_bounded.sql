-- TiDB SQL: enforce/adjust max_slots semantics and vacancy queries
-- 1) Add column (if not already present) and backfill to at least 10
ALTER TABLE department_position_titles_hr2
  ADD COLUMN IF NOT EXISTS max_slots INT NULL;

-- 2) Backfill null or very small values to be within [10,30]
-- Set NULL or values < 10 to 10
UPDATE department_position_titles_hr2
SET max_slots = 10
WHERE max_slots IS NULL OR max_slots < 10;

-- Cap any values > 30 down to 30
UPDATE department_position_titles_hr2
SET max_slots = 30
WHERE max_slots > 30;

-- Optional: If you want a DB-level default of 10 for new rows, enable this (uncomment)
-- ALTER TABLE department_position_titles_hr2
--   MODIFY COLUMN max_slots INT NOT NULL DEFAULT 10;

-- 3) Helpful indexes
CREATE INDEX IF NOT EXISTS idx_positions_department_id ON department_position_titles_hr2(department_id);
CREATE INDEX IF NOT EXISTS idx_employees_position_id ON employees(position_id);
CREATE INDEX IF NOT EXISTS idx_employees_department_id ON employees(department_id);
CREATE INDEX IF NOT EXISTS idx_specializations_deptcode ON department_specializations_hr2(dept_code);

-- 4) Vacant positions query (applies bounds: max_slots is already clamped to [10,30] by backfill)
SELECT
  p.id,
  p.position_title,
  p.department_id,
  COALESCE(d.name, '') AS department_name,
  COALESCE(p.max_slots, 10) AS max_slots,
  COALESCE(e.assigned_count, 0) AS assigned_count,
  GREATEST(COALESCE(p.max_slots, 10) - COALESCE(e.assigned_count, 0), 0) AS available_slots
FROM department_position_titles_hr2 p
LEFT JOIN departments_hr2 d ON p.department_id = d.department_id
LEFT JOIN (
  SELECT position_id, COUNT(*) AS assigned_count
  FROM employees
  WHERE position_id IS NOT NULL
  GROUP BY position_id
) e ON e.position_id = p.id
WHERE GREATEST(COALESCE(p.max_slots, 10) - COALESCE(e.assigned_count, 0), 0) > 0
ORDER BY available_slots DESC, department_name, p.position_title;

-- 5) Available specializations per department (unchanged logic)
SELECT
  d.department_id,
  d.name AS department_name,
  s.specialization_name
FROM department_specializations_hr2 s
JOIN departments_hr2 d ON s.dept_code = d.department_id
LEFT JOIN (
  SELECT department_id, TRIM(specialization) AS specialization
  FROM employees
  WHERE specialization IS NOT NULL AND TRIM(specialization) <> ''
  GROUP BY department_id, TRIM(specialization)
) e ON e.department_id = d.department_id
     AND e.specialization = s.specialization_name
WHERE s.is_active = 1
  AND e.specialization IS NULL
ORDER BY d.name, s.specialization_name;

-- 6) Department summary (Assigned / Max / Available / Available specializations)
SELECT
  d.department_id,
  d.name AS department_name,
  COALESCE(emp.assigned_count, 0) AS assigned,
  COALESCE(pos.total_max, 0) AS max,
  GREATEST(COALESCE(pos.total_max, 0) - COALESCE(emp.assigned_count, 0), 0) AS available,
  COALESCE(specs.available_specializations, 0) AS available_specializations
FROM departments_hr2 d
LEFT JOIN (
  SELECT department_id, COUNT(*) AS assigned_count
  FROM employees
  GROUP BY department_id
) emp ON emp.department_id = d.department_id
LEFT JOIN (
  SELECT department_id, SUM(COALESCE(max_slots, 10)) AS total_max
  FROM department_position_titles_hr2
  GROUP BY department_id
) pos ON pos.department_id = d.department_id
LEFT JOIN (
  SELECT ds.dept_code AS department_id, COUNT(*) AS available_specializations
  FROM department_specializations_hr2 ds
  LEFT JOIN (
    SELECT department_id, TRIM(specialization) AS specialization
    FROM employees
    WHERE specialization IS NOT NULL AND TRIM(specialization) <> ''
    GROUP BY department_id, TRIM(specialization)
  ) e ON e.department_id = ds.dept_code
       AND e.specialization = ds.specialization_name
  WHERE ds.is_active = 1
    AND e.specialization IS NULL
  GROUP BY ds.dept_code
) specs ON specs.department_id = d.department_id
ORDER BY d.name;
