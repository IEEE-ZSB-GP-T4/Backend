import pandas as pd
import numpy as np
from datetime import datetime, timedelta
import json
import os

#عشان مسارات فايلات الداتا تبقا dynamic 

DATA_DIR = os.environ.get("DATA_DIR", os.path.join(os.path.dirname(__file__), "..", "data"))

def get_processed_data():
     
    df_users = pd.read_csv(os.path.join(DATA_DIR, 'users.csv'))
    df_courses = pd.read_csv(os.path.join(DATA_DIR, 'courses.csv'))
    df_study_plans = pd.read_csv(os.path.join(DATA_DIR, 'study_plans.csv'))
    df_tasks = pd.read_csv(os.path.join(DATA_DIR, 'tasks.csv'))

    # to drop any duplicated task and keep the last updated one
    df_tasks = df_tasks.drop_duplicates(subset=['course_id', 'title', 'description'], keep='last')
    
   
    df_tasks['created_at'] = pd.to_datetime(df_tasks['created_at'])
    df_tasks['deadline'] = pd.to_datetime(df_tasks['deadline'])
    df_study_plans['created_at'] = pd.to_datetime(df_study_plans['created_at'])
    
   
    return {
        "users": df_users,
        "courses": df_courses,
        "study_plans": df_study_plans, 
        "tasks": df_tasks
    }

 