import pandas as pd
import plotly.express as px

# Palette Planora
PLANORA_PALETTE = ["#0A369D", "#4472CA", "#5E7CE2", "#92B4F4", "#CFDEE7"]

def generate_user_visuals(data_dict, target_user_id):
    # getting data and filter it
    df_courses = data_dict['courses']
    df_tasks = data_dict['tasks']

    user_courses = df_courses[df_courses['user_id'] == target_user_id]['id'].tolist()
    user_tasks = df_tasks[df_tasks['course_id'].isin(user_courses)]

    if user_tasks.empty:
        return {"tasks_status_donut": {}, "tasks_by_priority_bar": {}}

    # Donut Chart (Tasks status)
    status_counts = user_tasks.groupby('status').size().reset_index(name='count')
    fig_donut = px.pie(
        status_counts, 
        values='count', 
        names='status', 
        hole=0.5,
        title='Task Status Overview',
        color_discrete_sequence=PLANORA_PALETTE,
        template="none"
    )
    # showing numbers and labels in the Donut
    fig_donut.update_traces(
        textinfo='label+value',
        textposition='inside'
    )
    
    # storing the chart in dictionary form وتخفيف الـ JSON
    donut_dict = fig_donut.to_dict()
    donut_dict.get("layout", {}).pop("template", None)

    # Grouped Bar Chart (Tasks priority)
    priority_grouped = user_tasks.groupby(['priority', 'status']).size().reset_index(name='count')
    fig_bar = px.bar(
        priority_grouped, 
        x='priority', 
        y='count', 
        color='status', 
        barmode='group',
        text='count',  # showing nubers above the Bar
        title='Tasks Status by Priority',
        color_discrete_sequence=PLANORA_PALETTE,
        template="none"
    )
    # Labeled numders
    fig_bar.update_traces(
        textposition='outside'
    )
    
    bar_dict = fig_bar.to_dict()
    bar_dict.get("layout", {}).pop("template", None)
    
    return {
        "tasks_status_donut": donut_dict,
        "tasks_by_priority_bar": bar_dict
    }
