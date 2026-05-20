export interface PlanGoalStep {
    id: number;
    title: string;
    completed: boolean;
}

export interface PlanGoal {
    id: number;
    title: string;
    completed: boolean;
    steps: PlanGoalStep[];
}

export interface Plan {
    id: number;
    year: number;
    month: number;
    main_goal: string | null;
    goals: PlanGoal[];
}
