-- Seed data for gomuauth package schema 'account'
-- Run this after migrations

-- Permissions
INSERT INTO account.permissions (id, permission_key, module_name) VALUES
('550e8400-e29b-41d4-a716-446655440001', 'view_users', 'User Management'),
('550e8400-e29b-41d4-a716-446655440002', 'create_users', 'User Management'),
('550e8400-e29b-41d4-a716-446655440003', 'edit_users', 'User Management'),
('550e8400-e29b-41d4-a716-446655440004', 'delete_users', 'User Management'),
('550e8400-e29b-41d4-a716-446655440005', 'view_employees', 'Employee Management'),
('550e8400-e29b-41d4-a716-446655440006', 'create_employees', 'Employee Management'),
('550e8400-e29b-41d4-a716-446655440007', 'edit_employees', 'Employee Management'),
('550e8400-e29b-41d4-a716-446655440008', 'delete_employees', 'Employee Management'),
('550e8400-e29b-41d4-a716-446655440009', 'view_departments', 'Department Management'),
('550e8400-e29b-41d4-a716-446655440010', 'create_departments', 'Department Management'),
('550e8400-e29b-41d4-a716-446655440011', 'edit_departments', 'Department Management'),
('550e8400-e29b-41d4-a716-446655440012', 'delete_departments', 'Department Management');

-- Departments
INSERT INTO account.departments (id, name, code) VALUES
('660e8400-e29b-41d4-a716-446655440001', 'Human Resources', 'HR'),
('660e8400-e29b-41d4-a716-446655440002', 'Information Technology', 'IT'),
('660e8400-e29b-41d4-a716-446655440003', 'Finance', 'FIN'),
('660e8400-e29b-41d4-a716-446655440004', 'Operations', 'OPS');

-- Job Levels
INSERT INTO account.job_levels (id, level_name, level_rank) VALUES
('770e8400-e29b-41d4-a716-446655440001', 'Entry Level', 1),
('770e8400-e29b-41d4-a716-446655440002', 'Junior', 2),
('770e8400-e29b-41d4-a716-446655440003', 'Mid Level', 3),
('770e8400-e29b-41d4-a716-446655440004', 'Senior', 4),
('770e8400-e29b-41d4-a716-446655440005', 'Lead', 5),
('770e8400-e29b-41d4-a716-446655440006', 'Manager', 6),
('770e8400-e29b-41d4-a716-446655440007', 'Director', 7);

-- Job Positions (sample)
INSERT INTO account.job_positions (id, title, department_id, job_level_id) VALUES
('880e8400-e29b-41d4-a716-446655440001', 'HR Manager', '660e8400-e29b-41d4-a716-446655440001', '770e8400-e29b-41d4-a716-446655440006'),
('880e8400-e29b-41d4-a716-446655440002', 'Software Developer', '660e8400-e29b-41d4-a716-446655440002', '770e8400-e29b-41d4-a716-446655440003'),
('880e8400-e29b-41d4-a716-446655440003', 'Accountant', '660e8400-e29b-41d4-a716-446655440003', '770e8400-e29b-41d4-a716-446655440004'),
('880e8400-e29b-41d4-a716-446655440004', 'Operations Supervisor', '660e8400-e29b-41d4-a716-446655440004', '770e8400-e29b-41d4-a716-446655440005');

-- Superadmin User
INSERT INTO account.users (id, username, email, password_hash, user_type, force_password_change) VALUES
('990e8400-e29b-41d4-a716-446655440001', 'superadmin', 'superadmin@gomu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'internal', false); -- password: password