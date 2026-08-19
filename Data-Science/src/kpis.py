import pandas as pd

def calculate_user_kpis(data_dict, target_user_id):

  # droping duplicates first for any duplicated course in tasks

    df_courses = data_dict['courses']
    df_tasks = data_dict['tasks']
    df_plans = data_dict['study_plans']

    user_courses = df_courses[df_courses['user_id'] == target_user_id]['id'].tolist()
    user_tasks = df_tasks[df_tasks['course_id'].isin(user_courses)]
    user_plans = df_plans[df_plans['user_id'] == target_user_id]

    #Task Completion Rate
    total_tasks = len(user_tasks)
    completed_tasks = len(user_tasks[user_tasks['status'] == 'completed'])
    task_completion_rate = (completed_tasks / total_tasks) * 100 if total_tasks > 0 else 0.0

    #Time Utilization Rate
    available_hours = float(user_plans['available_hours'].sum())
    completed_hours = float(user_tasks[user_tasks['status'] == 'completed']['estimated_hours'].sum())
    time_utilization_rate = (completed_hours / available_hours) * 100 if available_hours > 0 else 0.0

    #Overall Productivity Score
    overall_productivity = (task_completion_rate * 0.6) + (time_utilization_rate * 0.4)

    # returning results in dictionary form
    return {
        "user_id": target_user_id,
        "task_completion_rate": round(task_completion_rate, 2),
        "time_utilization_rate": round(time_utilization_rate, 2),
        "overall_productivity_score": round(overall_productivity, 2)
    }