#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "ht.h"

#define MIN(a,b) (((a)<(b))?(a):(b))
#define MAX(a,b) (((a)>(b))?(a):(b))

typedef struct blueprint {
	int id;
	struct {
		int ore;
	} oreRobot;
	struct {
		int ore;
	} clayRobot;
	struct {
		int ore;
		int clay;
	} obsidianRobot;
	struct {
		int ore;
		int obsidian;
	} geodeRobot;
} blueprint_t;

typedef struct state {
	int ore;
	int clay;
	int obsidian;
	int geode;
	int oreRobots;
	int clayRobots;
	int obsidianRobots;
	int geodeRobots;
} state_t;

struct QNode {
	state_t *state;
	struct QNode *next;
};

struct Queue {
	int length;
	struct QNode *front, *rear;
	ht *hashtable;
};


struct QNode* newNode(state_t *s) {
	struct QNode *temp = (struct QNode*)malloc(sizeof(struct QNode));
	temp->state = s;
	temp->next = NULL;
	return temp;
}

struct Queue* createQueue() {
	struct Queue *q = (struct Queue*)malloc(sizeof(struct Queue));
	q->front = q->rear = NULL;
	q->length = 0;
	q->hashtable = ht_create();
	return q;
}

void freeQueue(struct Queue *q) {
	ht_destroy(q->hashtable);
	free(q);
}

void enQueue(struct Queue *q, state_t *s) {
	struct QNode *temp = newNode(s);
	q->length++;
	if (q->rear == NULL) {
		q->front = q->rear = temp;
		return;
	}
	q->rear->next = temp;
	q->rear = temp;
}

void enQueueIfNotExists(struct Queue *q, state_t *s) {
	char key[255];
	sprintf(key, "%d,%d,%d,%d,%d,%d,%d,%d",
			s->ore,
			s->clay,
			s->obsidian,
			s->geode,
			s->oreRobots,
			s->clayRobots,
			s->obsidianRobots,
			s->geodeRobots);
	if (ht_get(q->hashtable, key)) {
		return;
	}
	ht_set(q->hashtable, key, s);
	enQueue(q, s);
}

state_t *dequeue(struct Queue *q) {
	if (q->front == NULL)
		return NULL;
	struct QNode *temp = q->front;
	q->front = q->front->next;
	if (q->front == NULL)
		q->rear = NULL;
	state_t *s = temp->state;
	q->length--;
	free(temp);
	return s;
}

state_t *clone_state(state_t *state) {
	state_t *new = (state_t *) malloc(sizeof(state_t));
	memcpy(new, state, sizeof(state_t));
	return new;
}

int getQualityLevel(blueprint_t *blueprint, int m) {

	int maxOrePerMinute = MAX(MAX(MAX(blueprint->oreRobot.ore, blueprint->clayRobot.ore), blueprint->obsidianRobot.ore), blueprint->geodeRobot.ore);

	state_t *state = (state_t *) calloc(1, sizeof(state_t));
	state->oreRobots = 1;

	int res = 0;

	struct Queue *q = createQueue();
	enQueue(q, state);

	for (int d = m; d >= 0; d--) {

		struct Queue *ndq = createQueue();

		while ((state = dequeue(q))) {
			int ore = MIN(state->ore, maxOrePerMinute * d - (state->oreRobots * ( d - 1 )));
			int clay = MIN(state->clay, blueprint->obsidianRobot.clay * d - (state->clayRobots * ( d - 1 )));
			int obsidian = MIN(state->obsidian, blueprint->geodeRobot.obsidian * d - ( state->obsidianRobots * ( d - 1 )));

			if (d == 0) {
				res = MAX(res, state->geode);
				free(state);
				continue;
			}

			state->ore = ore + state->oreRobots;
			state->clay = clay + state->clayRobots;
			state->obsidian = obsidian + state->obsidianRobots;
			state->geode += state->geodeRobots;

			enQueueIfNotExists(ndq, state);

			if (state->oreRobots <= maxOrePerMinute && ore >= blueprint->oreRobot.ore) {
				state_t *new = clone_state(state);
				new->ore -= blueprint->oreRobot.ore;
				new->oreRobots++;
				enQueueIfNotExists(ndq, new);
			}
			if (state->clayRobots <= blueprint->obsidianRobot.clay && ore >= blueprint->clayRobot.ore) {
				state_t *new = clone_state(state);
				new->ore -= blueprint->clayRobot.ore;
				new->clayRobots++;
				enQueueIfNotExists(ndq, new);
			}
			if (state->obsidianRobots <= blueprint->geodeRobot.obsidian && ore >= blueprint->obsidianRobot.ore && clay >= blueprint->obsidianRobot.clay) {
				state_t *new = clone_state(state);
				new->ore -= blueprint->obsidianRobot.ore;
				new->clay -= blueprint->obsidianRobot.clay;
				new->obsidianRobots++;
				enQueueIfNotExists(ndq, new);
			}
			if (ore >= blueprint->geodeRobot.ore && obsidian >= blueprint->geodeRobot.obsidian) {
				state_t *new = clone_state(state);
				new->ore -= blueprint->geodeRobot.ore;
				new->obsidian -= blueprint->geodeRobot.obsidian;
				new->geodeRobots++;
				enQueueIfNotExists(ndq, new);
			}
		}
		freeQueue(q);
		q = ndq;
	}

	return res;
}

int main() {
	blueprint_t blueprint = { 0 };
	int res1 = 0, res2 = 1;
	while (scanf("Blueprint %d: Each ore robot costs %d ore. Each clay robot costs %d ore. Each obsidian robot costs %d ore and %d clay. Each geode robot costs %d ore and %d obsidian.\n",
				 &blueprint.id, &blueprint.oreRobot.ore, &blueprint.clayRobot.ore, &blueprint.obsidianRobot.ore, &blueprint.obsidianRobot.clay, &blueprint.geodeRobot.ore, &blueprint.geodeRobot.obsidian) != EOF) {
		res1 += blueprint.id * getQualityLevel(&blueprint, 24);
		if (blueprint.id <= 3) {
			res2 *= getQualityLevel(&blueprint, 32);
	   }
	}
	printf("Result 1: %d\nResult 2: %d\n", res1, res2);
}
